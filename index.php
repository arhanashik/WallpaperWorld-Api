<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <link href="css/ww.css" rel="stylesheet"/>

    <title>Wallpaper World</title>
  </head>
  <body>
    <div class="container">
    <div class="row">
      <div class="col-md-6">
          <form method="post" action="Api.php?call=upload" enctype="multipart/form-data">
            <div class="form-group files color">
              <label>Select wallpaper </label>
              <input type="file" class="form-control" id="file" name="file" required>
            </div>
            <div class="form-group">
              <label for="title">Wallpaper title</label>
              <input type="text" class="form-control" id="title" name="title" placeholder="" required>
            </div>
            <div class="form-group">
              <label for="tag">Wallpaper tag</label>
              <select class="form-control" id="tag" name="tag">
                <option>Photography</option>
                <option>Nature</option>
                <option>Art</option>
                <option>Animal</option>
                <option>Love</option>
                <option>Life</option>
                <option>Travel</option>
                <option>Color</option>
              </select>
            </div>
            <div class="form-group">
              <label for="price">Wallpaper price</label>
              <input type="number" class="form-control" id="price" name="price" value="0">
            </div>
            <input type="hidden" class="form-control" id="uploader_id" name="uploader_id" value="1">
            <button type="submit" class="btn btn-primary">Upload Wallpaper</button>
          </form>
        </div>
      </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
  </body>
</html>