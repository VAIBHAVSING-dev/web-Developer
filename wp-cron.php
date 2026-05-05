<?php
include_once(__DIR__ . "/include/connection.php");

date_default_timezone_set("Asia/Calcutta");

function cron_current_period_id()
{
    $startOfDay = strtotime(date("Y-m-d 00:00:00"));
    $elapsed = time() - $startOfDay;
    $periodNumber = (int)floor($elapsed / 180) + 1;
    if ($periodNumber < 1) {
        $periodNumber = 1;
    } else if ($periodNumber > 480) {
        $periodNumber = 480;
    }

    return date('Ymd') . sprintf("%03d", $periodNumber);
}

function cron_latest_period_id($con)
{
    $query = mysqli_query($con, "SELECT `gameid` FROM `tbl_gameid` ORDER BY `id` DESC LIMIT 1");
    if (!$query || mysqli_num_rows($query) == 0) {
        return '';
    }

    $row = mysqli_fetch_assoc($query);
    return isset($row['gameid']) ? $row['gameid'] : '';
}

function cron_period_exists($con, $periodid)
{
    $periodid = mysqli_real_escape_string($con, $periodid);
    $query = mysqli_query($con, "SELECT `id` FROM `tbl_gameid` WHERE `gameid` = '".$periodid."' LIMIT 1");
    return $query && mysqli_num_rows($query) > 0;
}

function cron_open_period($con, $periodid)
{
    if ($periodid == '' || cron_period_exists($con, $periodid)) {
        return false;
    }

    $periodid = mysqli_real_escape_string($con, $periodid);
    return mysqli_query($con, "INSERT INTO `tbl_gameid` (`gameid`) VALUES ('".$periodid."')");
}

function cron_has_tab_results($con, $periodid)
{
    $periodid = mysqli_real_escape_string($con, $periodid);
    $query = mysqli_query($con, "SELECT COUNT(*) AS total FROM `tbl_result` WHERE `periodid` = '".$periodid."' AND `tabtype` IN ('parity','sapre','bcone','emerd')");
    if (!$query) {
        return false;
    }

    $row = mysqli_fetch_assoc($query);
    return isset($row['total']) && (int)$row['total'] >= 4;
}

function cron_settle_existing_results($con, $periodid)
{
    if ($periodid == '') {
        return false;
    }

    include_once(__DIR__ . "/userResult.php");

    $periodid = mysqli_real_escape_string($con, $periodid);
    $query = mysqli_query($con, "
        SELECT r.`periodid`, r.`tabtype`, r.`resulttype`, r.`result`, r.`randomresult`, r.`color`, r.`randomcolor`, r.`randomprice`
        FROM `tbl_result` r
        WHERE r.`periodid` = '".$periodid."'
            AND r.`tabtype` IN ('parity','sapre','bcone','emerd')
    ");

    if (!$query) {
        return false;
    }

    $settled = false;
    while ($row = mysqli_fetch_assoc($query)) {
        $number = ($row['resulttype'] == 'real') ? $row['result'] : $row['randomresult'];
        $color = ($row['resulttype'] == 'real') ? $row['color'] : $row['randomcolor'];
        resultbyUser($con, $row['periodid'], $number, $color, $row['randomprice'], $row['tabtype']);
        $settled = true;
    }

    return $settled;
}

function cron_settle_period($con, $periodid)
{
    global $numbermappings;

    if ($periodid == '') {
        return false;
    }

    if (cron_has_tab_results($con, $periodid)) {
        return cron_settle_existing_results($con, $periodid);
    }

    include(__DIR__ . "/winnerResult.php");
    cron_settle_existing_results($con, $periodid);
    return true;
}

$currentPeriod = cron_current_period_id();
$latestPeriod = cron_latest_period_id($con);
$settled = array();

if ($latestPeriod == '') {
    cron_open_period($con, $currentPeriod);
    echo "initialized=".$currentPeriod;
    exit;
}

$unsettledQuery = mysqli_query($con, "
    SELECT DISTINCT b.`periodid`
    FROM `tbl_betting` b
    LEFT JOIN `tbl_userresult` ur
        ON ur.`periodid` = b.`periodid`
        AND ur.`tab` = b.`tab`
        AND ur.`userid` = b.`userid`
        AND ur.`type` = b.`type`
        AND ur.`value` = b.`value`
    WHERE b.`tab` IN ('parity','sapre','bcone','emerd')
        AND b.`periodid` < '".$currentPeriod."'
    GROUP BY b.`periodid`, b.`userid`, b.`tab`, b.`type`, b.`value`
    HAVING COUNT(DISTINCT b.`id`) > COUNT(DISTINCT ur.`id`)
    ORDER BY b.`periodid` ASC
    LIMIT 1
");

if ($unsettledQuery) {
    while ($row = mysqli_fetch_assoc($unsettledQuery)) {
        $periodid = $row['periodid'];
        if (cron_settle_period($con, $periodid)) {
            $settled[] = $periodid;
        }
    }
}

if ($latestPeriod < $currentPeriod && !in_array($latestPeriod, $settled)) {
    if (cron_settle_period($con, $latestPeriod)) {
        $settled[] = $latestPeriod;
    }
}

cron_open_period($con, $currentPeriod);

echo "current=".$currentPeriod;
echo "; latest=".$latestPeriod;
echo "; settled=".implode(",", array_unique($settled));
echo PHP_EOL;
?>
