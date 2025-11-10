
<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_name'] !== 'Tourist') {
    header('Location: ../../index.php');
    exit;
} else if ($_SESSION['user']['account_status'] == 'Suspended') {
    header('Location: account-suspension.php');
    exit;
} else if ($_SESSION['user']['account_status'] == 'Pending') {
    header('Location: account-pending.php');
    exit;
}

require_once "../../classes/tourist.php";
require_once "../../classes/tour-manager.php";

$tourist_ID = $_SESSION['user']['account_ID'];
$touristObj = new Tourist();
$packageObj = new TourManager();

$filter = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    





}




// Get available packages
$packages = $packageObj->viewAllPackages();
$packageCategory = $packageObj->getTourSpotsCategory(); // adjust method name if needed
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tourismo Zamboanga</title>

    <link rel="stylesheet" href="/../../assets/css/header.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../assets/css/tourist/index.css">

    
</head>
<body>

<header class = "header">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">Tourismo Zamboanga</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Tour Packages</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Tour Spots</a></li>
            </ul>
            <a href="logout.php" class="btn btn-info ms-lg-3">Log out </a>
            </div>
        </div>
        </nav>

</header>

<button 
    class="filter-toggle btn btn-warning d-md-none position-fixed bottom-0 start-0 m-3 shadow-lg rounded-circle p-0 d-flex align-items-center justify-content-center" 
    id="filterToggle" 
    aria-label="Open filters"
    style="width: 3rem; height: 3rem;">
    <i class="bi bi-funnel-fill fs-4"></i>
</button>

<!-- 2. Overlay (darkens page) -->
<div class="filter-overlay d-md-none"></div>

<aside id="filterSidebar" class="aside-tourist p-3 bg-light border rounded shadow-sm">
    <form action="" method="post">
            <h4 class="text-dark mb-3 border-bottom pb-2"><i class="bi-filter bi bi-funnel-fill"></i>Filter</h4>

        <div class="mb-4">
            <h6 class="fw-bold mb-2">Categories</h6>

            <?php foreach ($packageCategory as $p){
                if (!isset($p['spots_category'])) continue;
                $category = htmlspecialchars($p['spots_category']);
                $id = 'cat_' . preg_replace('/\s+/', '_', strtolower($category));
            ?>

                <div class="form-check">
                    <input 
                    class="form-check-input" 
                    type="checkbox" 
                    id="<?= $id; ?>" 
                    name="categories[]" 
                    value="<?= $category; ?>">
                    <label class="form-check-label" for="<?= $id; ?>">
                    <?= $category; ?>
                    </label>
                </div>
            <?php } ?>

            
        </div>

        <!-- Price -->
        <div class="mb-4">
            <h6 class="fw-bold mb-2">Price</h6>

            <div class="input-group mb-2">
                <span class="input-group-text">₱</span>
                <input 
                type="number" 
                id="priceValue" 
                class="form-control" 
                min="500" max="10000" step="500" 
                value="5000"
                >
            </div>

            <input 
                type="range" 
                class="form-range" 
                id="priceRange" 
                min="500" max="10000" step="500" 
                value="5000"
            >
        </div>

        <!-- PAX (with Min & Max input) -->
        <div class="mb-4">
            <h6 class="fw-bold mb-2">PAX</h6>
            <div class="row g-2 align-items-center">
            <div class="col">
                <label for="minPax" class="form-label small text-muted">Min</label>
                <input type="number" class="form-control" id="minPax" min="1" placeholder="1">
            </div>
            <div class="col">
                <label for="maxPax" class="form-label small text-muted">Max</label>
                <input type="number" class="form-control" id="maxPax" min="1" placeholder="10">
            </div>
            </div>
        </div>

        <!-- Apply Button -->
        <button class="btn-filter btn btn-warning w-100 text-white fw-semibold">Apply Filters</button>
    </form>
</aside>
<main class = "main-contents">
<div class="container">
  <section class="mx-auto my-5" style="max-width: 23rem;">
      
    <div class="card">
      <div class="bg-image hover-overlay ripple" data-mdb-ripple-color="light">
        <img src="https://mdbootstrap.com/img/Photos/Horizontal/Food/8-col/img (5).jpg" class="img-fluid" />
        <a href="#!">
          <div class="mask" style="background-color: rgba(251, 251, 251, 0.15);"></div>
        </a>
      </div>
      <div class="card-body">
        <h5 class="card-title font-weight-bold"><a>La Sirena restaurant</a></h5>
        <ul class="list-unstyled list-inline mb-0">
          <li class="list-inline-item me-0">
            <i class="fas fa-star text-warning fa-xs"> </i>
          </li>
          <li class="list-inline-item me-0">
            <i class="fas fa-star text-warning fa-xs"></i>
          </li>
          <li class="list-inline-item me-0">
            <i class="fas fa-star text-warning fa-xs"></i>
          </li>
          <li class="list-inline-item me-0">
            <i class="fas fa-star text-warning fa-xs"></i>
          </li>
          <li class="list-inline-item">
            <i class="fas fa-star-half-alt text-warning fa-xs"></i>
          </li>
          <li class="list-inline-item">
            <p class="text-muted">4.5 (413)</p>
          </li>
        </ul>
        <p class="mb-2">$ • American, Restaurant</p>
        <p class="card-text">
          Some quick example text to build on the card title and make up the bulk of the
          card's content.
        </p>
        <hr class="my-4" />
        <p class="lead"><strong>Tonight's availability</strong></p>
        <ul class="list-unstyled list-inline d-flex justify-content-between">
          <li class="list-inline-item me-0">
            <div class="chip me-0">5:30PM</div>
          </li>
          <li class="list-inline-item me-0">
            <div class="chip bg-secondary text-white me-0">7:30PM</div>
          </li>
          <li class="list-inline-item me-0">
            <div class="chip me-0">8:00PM</div>
          </li>
          <li class="list-inline-item me-0">
            <div class="chip me-0">9:00PM</div>
          </li>
        </ul>
        <a href="#!" class="btn btn-link link-secondary p-md-1 mb-0">Button</a>
      </div>
    </div>



</main>


<script>
  const priceInput = document.getElementById('priceValue');
  const priceRange = document.getElementById('priceRange');

  // Update number input when slider changes
  priceRange.addEventListener('input', () => {
    priceInput.value = priceRange.value;
  });

  // Update slider when number input changes
  priceInput.addEventListener('input', () => {
    let value = parseInt(priceInput.value) || 0;
    if (value < 500) value = 500;
    if (value > 10000) value = 10000;
    priceInput.value = value;
    priceRange.value = value;
  });


  // for resizing aside
    document.addEventListener("DOMContentLoaded", () => {
        const toggleBtn = document.getElementById("filterToggle");
        const sidebar = document.getElementById("filterSidebar");
        const overlay = document.querySelector(".filter-overlay");

        toggleBtn?.addEventListener("click", () => {
            sidebar.classList.add("active");
            overlay.classList.add("active");
        });

        overlay?.addEventListener("click", () => {
            sidebar.classList.remove("active");
            overlay.classList.remove("active");
        });
    });

</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
