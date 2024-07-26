<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket system</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        body {
            background-color:black;
            color: white;
            padding-top: 80px;
        }
        .jumbotron {
            background-image: url('nissan_image.jpg'); /* Replace with your Nissan image URL */
            background-size: cover;
            height: 600px;
            text-align: center;
            color: #ffffff;
            padding-top: 200px; /* Adjust as needed */
        }
        .jumbotron h1 {
            font-size: 3.5rem;
        }
        .jumbotron p {
            font-size: 1.5rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <a class="navbar-brand" href="#">Team Swatt</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link btn btn-warning mx-2" href="login.php">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn btn-primary mx-2" href="register.php">Register</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="jumbotron">
        <div class="container w-50 m-auto">
            <h1 class="display-4">Team Swat Shuttles Ticket Booking System</h1>
            <p class="lead">Book your tickets with ease and comfort.</p>
            <p>
                <a class="btn btn-primary btn-lg" href="register.php" role="button">Register</a>
                <a class="btn btn-success btn-lg" href="login.php" role="button">Login</a>
            </p>
        </div>
    </div>

    <div class="container my-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <h2>About Nissan Transportation</h2>
                Explore convenience and reliability with Team SWAT Shuttles, your ultimate solution for seamless transportation across Kenya. Whether you're commuting daily or planning a weekend getaway, we're here to simplify your travel experience.            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
