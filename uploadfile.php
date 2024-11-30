<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
        <form action="uploadImages.php" method="post" enctype="multipart/form-data">
            <input type="file" name="images[]" id="images" multiple>
            <input type="text" name="purpose" id="purpose" value="questions">
            <input type="submit" value="Upload">
        </form>
</body>
</html>