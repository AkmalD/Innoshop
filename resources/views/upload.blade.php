<!DOCTYPE html>
<html>
<head>
    <title>Upload and Optimize Image</title>
</head>
<body>
    <h1>Upload Image</h1>
    <form action="/upload-image" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="image" required>
        <button type="submit">Upload and Optimize</button>
    </form>
</body>
</html>
