<?php
  
include 'includes/MailChimp.php';

/* Global */

$captcha = "";
$captchaError = "";

$userName = "";
$nameError = "";

$userEmail = "";
$emailError = "";

/* Get values from form */

if(isset($_POST['submit'])) {

  if(isset($_POST['g-recaptcha-response'])){

   $captcha=$_POST['g-recaptcha-response'];

  }

  if (empty($_POST['name'])) {

   $userError = "<label class='message error'><i class='fa fa-times' aria-hidden='true'></i> Please type your name.</label>";

  } else {

   $userName = trim($_POST['name']);
   $userName = ucwords($userName);

  }

  if (empty($_POST['email'])) {

   $emailError = "<label class='message error'><i class='fa fa-times' aria-hidden='true'></i> Please type your email.</label>";

  } else {

   $userEmail = trim($_POST['email']);

  }

  $secret = "PRIVATE_KEY";
  $ip = $_SERVER['REMOTE_ADDR'];
  $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secret."&response=".$captcha."&remoteip=".$ip);
  $responseKeys = json_decode($response,true);

  if(intval($responseKeys["success"]) !== 1) {

   $captchaError = "<label class='error'><i class='fa fa-times' aria-hidden='true'></i> Please verify that you are human.</label>";

  } else {

   $userAgree = $_POST['userAgree'];

   if ($userAgree == "Yes") {

      $MailChimp = new MailChimp('MAILCHIMP_API');
      $list_id = 'LIST_ID';

      $result = $MailChimp->post("lists/$list_id/members", [
        'email_address' => $userEmail,
        'status'        => 'subscribed',
        'merge_fields'  => [
          'NAME'     => $userName
        ]
      ]);

      if ($MailChimp->success()) {

        $mailChimpSuccess = "<label class='message success'><i class='fa fa-check' aria-hidden='true'></i> You are subscribed!</label>";

      } else {

        $mailChimpError = "<label class='message error'><i class='fa fa-times' aria-hidden='true'></i>" . $MailChimp->getLastError() . "</label>";

      }

   } else {

    $agreeError = "<label class='message error'><i class='fa fa-times' aria-hidden='true'></i> Please check the box above.</label>";

   }

  }

}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="Alpha template">
    <meta name="author" content="Bronson Dunbar">

    <link rel="apple-touch-icon" sizes="57x57" href="assets/ico/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="assets/ico/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="assets/ico/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="assets/ico/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="assets/ico/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="assets/ico/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="assets/ico/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="assets/ico/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/ico/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/ico/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="assets/ico/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/ico/favicon-16x16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">

    <title>MailChimp API v3</title>

    <link href="css/style.css?v=2158" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="container">

      <header>
        <h1>MailChimp API v3</h1>

        <div class="panel panel-default">
          <div class="panel-body">
            <p>This example uses the MailChimp API v3 created by <a href="https://github.com/drewm/mailchimp-api" target="_blank">Drew McLellan</a>.</p>

            <p>It also includes Google reCAPTCHA to ensure there are no spam subscribes.</p>

            <p>I have also added a check that the person agrees to join the mailing list.</p>
          </div>
        </div>
      </header>

      <h2>Steps</h2>

      <p>In order for the example below to work you will need to use your own Google reCAPTCHA site &amp; private keys as well as your MailChimp API key and list ID</p>

      <ol>
        <li>Go to <a href="https://www.google.com/recaptcha/intro/">https://www.google.com/recaptcha/intro/</a></li>
        <li>Click on the Get reCAPTCHA button</li>
        <li>After logging in, you will need to register your site in order to get your keys.</li>
        <li>Once you have done this, you can select your site and then click on the Keys section</li>
        <li>This will give you both your site &amp; private keys</li>
        <li>In index.php, you can search for SITE_KEY and PRIVATE_KEY and replace these with your keys</li>
        <li>Next you need to log into your MailChimp account and get your API key and the list you want people to subscribe to.</li>
        <li>To get your MailChimp API key you can read this <a href="http://kb.mailchimp.com/integrations/api-integrations/about-api-keys" target="_blank">http://kb.mailchimp.com/integrations/api-integrations/about-api-keys</a></li>
        <li>To get your MailChimp list ID you can read this <a href="http://kb.mailchimp.com/lists/manage-contacts/find-your-list-id" target="_blank">http://kb.mailchimp.com/lists/manage-contacts/find-your-list-id</a></li>
        <li>After you have both your API key and list ID you can replace them in the index.php by searching for MAILCHIMP_API and LIST_ID</li>
        <li>Once this has been done, you can upload the files your site and test. Keep in mind you need to upload the files to the same site you added in Google reCAPTCHA</li>
      </ol>

      <h2>Example</h2>

      <form name="form" id="form" method="POST" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
        <label for="name">What is your name?</label>
        <?php echo $userError ?>
        <input id="name" name="name" class="form" placeholder="John" type="text" required>

        <label for="email">What is your email address?</label>
        <?php echo $emailError ?>
        <input id="email" name="email" class="form" placeholder="john@doe.com" type="email" required>

        <ul class="form-list">
          <li><input id="userAgree" name="userAgree" type="checkbox" value="Yes" ></li>
          <li><label for="userAgree">I agree to join the mailing list.</label></li>
        </ul>

        <?php echo $agreeError ?>

        <div class="g-recaptcha" data-sitekey="SITE_KEY"></div>
        <script src='https://www.google.com/recaptcha/api.js'></script>
        <?php echo $captchaError ?>

        <input id="submitForm" name="submit" type="submit" class="btn btn-lg btn-default" value="Sign me up">

        <?php echo $mailChimpError ?>
        <?php echo $mailChimpSuccess ?>
      </form>

    </div>
  </body>
</html>