<?php
/*
 Redirects to bahn.de public transport search with parameters parsed from url parameters
 designed for use with OpenSearch browser search engine.
 sample queries:
  hannover to braunschweig waage tomorrow 1230
  frankfurt
  tegel 430 a
*/

$default_home = 'Berlin';

$query = get_query();

$time_type = pop_time_type($query);
$time = pop_time($query);
$date = pop_date($query);
$to = extract_to($query);
$from = extract_from($query, $default_home);

print_debug($from, $to, $date, $time, $time_type);

redirect_to_bahn_api($from, $to, $date, $time, $time_type);


function get_query() {
    $query_string = 'München';
    if (isset($_GET['q']))
        $query_string = trim($_GET['q']);
    print("Query: $query_string</br>");

    $query_split = explode(' ', $query_string);
    return $query_split;
}

function pop_time_type(&$query) {
    if (end($query) == 'a') {
        array_pop($query);
        return 'arrive';
    }
    return 'depart';
}

function pop_time(&$query) {
    $literal = end($query);
    if (is_numeric($literal)) {
        array_pop($query);
        return extract_time($literal);
    }
    return extract_time(date('Hi')); //Hi being the format string for HHMM
}

function extract_time($literal) {
    $len = strlen($literal);
    if ($len == 4)
        return substr_replace($literal, ':', 2, 0);
    if ($len == 3)
        return '0' . substr_replace($literal, ':', 1, 0);
    if ($len == 2)
        return $literal . ':00';
    if ($len == 1)
        return '0' . $literal . ':00';
}

function pop_date(&$query) {
    $literal = end($query);
    if (is_date($literal)) {
        array_pop($query);
        return extract_date($literal);
    }
    return extract_date('today');
}

function is_date($literal) {
    $known_dates = ['today', 'tomorrow'];
    return in_array($literal, $known_dates);
}

function extract_date($literal) {
    switch ($literal) {
        case 'today':
            $timestamp = time();
            break;
        case 'tomorrow':
            $timestamp = strtotime('+1 day');
            break;
        default:
            $timestamp = time();
    }
    return format_date($timestamp); //'So, 10.07.16'
}

function format_date($date) {
    $weekdays = ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'];
    $weekday = $weekdays[date("w", $date)];
    $datestring = date("d.m.y", $date);

    return "$weekday, $datestring";
}

function extract_to($query) {
    if (has_explicit_to($query)) {
        if (has_to_and_from($query))
            return explode(' to ', join(' ', $query))[1];
        else
            return explode(' to ', join(' ', $query))[0];
    }
    return join(' ', $query);
}

function extract_from($query, $default_home) {
    if (has_explicit_to($query)) {
        if (has_to_and_from($query))
            return explode(' to ', join(' ', $query))[0];
    }
    return $default_home;
}

function has_to_and_from($query) {
    $toAndFrom = explode(' to ', join(' ', $query));
    return (count($toAndFrom) == 2);
}

function has_explicit_to($query) {
    return in_array('to', $query);
}

function print_debug($from, $to, $date, $time, $time_type) {
    echo("Interpretation: From <b>$from</b> to <b>$to</b> on <b>$date</b>, <b>$time_type</b> at <b>$time</b>");
}

function redirect_to_bahn_api($from, $to, $date, $time, $time_type) {
    $to = urlencode($to);
    $from = urlencode($from);
    $date = urlencode($date);
    $time = urlencode($time);
    $time_type = urlencode($time_type);

    header("Location: https://reiseauskunft.bahn.de/bin/query.exe/dn?country=DEU&ignoreTypeCheck=yes&S=$from&Z=$to&date=$date&time=$time&timesel=$time_type&optimize=0&start=1");
    die();
}

?>
