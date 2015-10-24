<?php
require __DIR__ . '/vendor/autoload.php';
require 'flickr.class.php';

use \Curl\Curl;

$flickr = new Flickr();
$flickr->authenticate();
?>
<!doctype html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Flickr Photo Upload</title>
</head>
<body>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $flickr->uploadPhoto();
    if ($result->error) {
        echo '<p>Photo upload failed.</p>';
    } else {
        $user_id = $_SESSION['user_id'];
        $photo_id = $result->response->photoid;
        $photo_url = 'http://www.flickr.com/photos/' . $user_id . '/' . $photo_id;
        echo '<p>Photo uploaded successfully. <a href="' . $photo_url . '">View photo</a>.</p>';
    }
}
?>

<form enctype="multipart/form-data" method="post">
    <fieldset>
        <legend>Flickr Photo Upload</legend>
        <label>Photo <input name="photo" type="file" /></label><br />
        <label>Title <input name="title" placeholder="Vacation (optional)" type="text" /></label><br />
        <label>Tags <input name="tags" placeholder="tropical,beach,vacation (optional)" type="text" /></label><br />
        <input type="submit" />
    </fieldset>
</form>

</body>
</html>
