<?php
/**
 * @file
 * 
 */

/* Load required lib files. */
session_start();
require_once('tqqoauth/tqqoauth.php');
require_once('config.php');

/* If access tokens are not available redirect to connect page. */
if (empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret'])) {
    header('Location: ./clearsessions.php');
}
/* Get user access tokens out of the session. */
$access_token = $_SESSION['access_token'];

/* Create a TqqOauth object with consumer/user tokens. */
$connection = new TqqOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);

/* If method is set change API call made. Test is called by default. */
$content = $connection->get('account/rate_limit_status');
echo "Current API hits remaining: {$content->remaining_hits}.";

/* Get logged in user to help with tests. */
$user = $connection->get('account/verify_credentials');

//$active = TRUE;
//if (empty($active) || empty($_GET['confirmed']) || $_GET['confirmed'] !== 'TRUE') {
  //echo '<h1>Warning! This page will make many requests to Tqq.</h1>';
  //echo '<h3>Performing these test might max out your rate limit.</h3>';
  //echo '<h3>Statuses/DMs will be created and deleted. Accounts will be un/followed.</h3>';
  //echo '<h3>Profile information/design will be changed.</h3>';
  //echo '<h2>USE A DEV ACCOUNT!</h2>';
  //echo '<h4>Before use you must set $active = TRUE in test.php</h4>';
  //echo '<a href="./test.php?confirmed=TRUE">Continue</a> or <a href="./index.php">go back</a>.';
  //exit;
//}

function tqqoauth_row($method, $response, $http_code, $parameters = '') {
  echo '<tr>';
  echo "<td><b>{$method}</b></td>";
  switch ($http_code) {
    case '200':
    case '304':
      $color = 'green';
      break;
    case '400':
    case '401':
    case '403':
    case '404':
    case '406':
      $color = 'red';
      break;
    case '500':
    case '502':
    case '503':
      $color = 'orange';
      break;
    default:
      $color = 'grey';
  }
  echo "<td style='background: {$color};'>{$http_code}</td>";
  if (!is_string($response)) {
    $response = print_r($response, TRUE);
  }
  if (!is_string($parameters)) {
    $parameters = print_r($parameters, TRUE);
  }
  echo '<td>', strlen($response), '</td>';
  echo '<td>', $parameters, '</td>';
  echo '</tr><tr>';
  #echo '<td colspan="4">', substr($response, 0, 800), '...</td>';
  echo '<td colspan="4"><pre>', $response, '</pre></td>';
  echo '</tr>';

}

function tqqoauth_header($header) {
  echo '<tr><th colspan="4" style="background: grey;">', $header, '</th></tr>';
}

/* Start table. */
echo '<br><br>';
echo '<table border="1" cellpadding="2" cellspacing="0">';
echo '<tr>';
echo '<th>API Method</th>';
echo '<th>HTTP Code</th>';
echo '<th>Response Length</th>';
echo '<th>Parameters</th>';
echo '</tr><tr>';
echo '<th colspan="4">Response Snippet</th>';
echo '</tr>';

$methods = array(
    'timeline' => array(
        array('GET', 'statuses/home_timeline', array("pagetime"=>0, "reqnum"=>20, 'pageflag'=>0) ),
        array('GET', 'statuses/public_timeline', array('pos'=>0, 'reqnum'=>20) ),
        array('GET', 'statuses/user_timeline', array('name'=>'talebook'),
        array('GET', 'statuses/mentions_timeline'),
        array('GET', 'statuses/ht_timeline'),
        array('GET', 'statuses/broadcast_timeline'),
    ),
    't blog' => array(
        array('POST', 't/add'),
        array('GET', 't/show'),
        array('DELETE', 't/del'),
        array('POST', 't/re_add'),
        array('POST', 't/reply'),
        array('POST', 't/add_pic'),
        array('POST', 't/re_count'),
        array('POST', 't/re_list'),
    ),
    'user info' => array(
        array('GET', 'user/info'),
        array('GET', 'user/update'),
        array('GET', 'user/otherinfo'),
    ),
    'friends' => array(
        array('GET', 'friends/fanslist'),
        array('GET', 'friends/idollist'),
        array('POST', 'friends/add'),
        array('POST', 'friends/addspecial'),
        array('GET', 'friends/check'),
        array('GET', 'friends/user_fanslist'),
        array('GET', 'friends/user_idollist'),
    ),
);


foreach ( $methods as $section => $actions ) {
    tqqoauth_header($section);
    foreach ( $actions as $action ) {
        $req = $action[0];
        $url = $action[1];
        $arg = array();
        if ( $req == 'GET' ){
            $status = $connection->get($url, $arg);
        }
        tqqoauth_row($url, $status, $connection->http_code);
    }
}

