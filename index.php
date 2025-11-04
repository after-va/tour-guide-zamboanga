<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tourismo Zamboanga</title>
    
    <!-- Bootstrap File -->
    <link rel="stylesheet" href="assets/css/bootstrap-grid.css">
    <link rel="stylesheet" href="assets/css/bootstrap-reboot.css">
    <link rel="stylesheet" href="assets/css/bootstrap.css">

    <!-- Vendor CSS Files
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet"> -->

    <!--Inner CSS Files -->
    
    
    <link rel="stylesheet" href="assets/css/index.css">
    <link rel="stylesheet" href="assets/css/header.css">
    

    <!-- ✅ Bootstrap 5.3.3 (CSS) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    
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
                <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Tour Packages</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Tour Spots</a></li>
            </ul>
            <a href="login.php" class="btn btn-info ms-lg-3">Sign In</a>
            </div>
        </div>
        </nav>

    </header>
    <main>
        <section id="hero">
        <!-- Slideshow background: multiple .slide children -->
            <div class="slideshow" aria-hidden="true">
                <div class="slide" style="background-image: url('assets/img/tour-spots/fort-pilar/1.jpg');"></div>
                <div class="slide" style="background-image: url('assets/img/tour-spots/great-santa-cruz-island/15.jpg');"></div>
                <div class="slide" style="background-image: url('assets/img/tour-spots/fort-pilar/2.jpg');"></div>
                <div class="slide" style="background-image: url('assets/img/tour-spots/fort-pilar/15.jpg');"></div>
                <div class="slide" style="background-image: url('assets/img/tour-spots/great-santa-cruz-island/4.jpg');"></div>
                <!-- add more .slide divs as needed -->
            </div>

            <!-- Foreground content (on top of slideshow) -->
            <div class="info d-flex align-items-center">
                <div class="container">
                <div class="row justify-content-center" data-aos="fade-up" data-aos-delay="100">
                    <div class="col-lg-8 text-center">
                    <h2>It's more Happy in Zamboanga!</h2>
                    <!-- <p>Connect with the Locals</p> -->
                    <a href="login.php" class="btn-get-started">Connect with a Local Guide Now!</a>
                    </div>
                </div>
                </div>
            </div>
        </section>

        <section id = "about">
            

        </section>

        <section id = "tour-spots">
            <div>
                <div class="card" style="width: 18rem;">
                    <img src="..." class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title">Card title</h5>
                        <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                        <a href="#" class="btn btn-primary">Go somewhere</a>
                    </div>
                </div>
            </div>
        </section>

        <section id = "marketing">

        </section>

        <section id = "category">

        </section>

        <section id = "tourpackages">

        </section>

    </main>


    <!-- Scripts -->
    <script>
        (function preloadImgs(urls){
            urls.forEach(u => {
            const img = new Image();
            img.src = u;
            });
        })([
            'assets/img/tour-spots/fort-pilar/1.jpg',
            'assets/img/tour-spots/fort-pilar/2.jpg',
            'assets/img/tour-spots/fort-pilar/15.jpg'
        ]);
        window.addEventListener("scroll", function() {
        const navbar = document.querySelector(".navbar");
        const hero = document.querySelector("#hero");

        if (window.scrollY > hero.offsetHeight - 80) {
            navbar.classList.add("scrolled");
        } else {
            navbar.classList.remove("scrolled");
        }
        });
    </script>



    <!-- Vendor JS Files
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>
    <script src="assets/vendor/aos/aos.js"></script>
    <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
    <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
    <script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
    <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script> -->

    <!-- ✅ Bootstrap JS (includes Popper.js for dropdowns/collapse) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>