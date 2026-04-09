<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="en">

<head>
   <title>Login Smart Parking</title>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">

   <link rel="icon" type="image/png" href="images/icons/favicon.ico" />

   <link rel="stylesheet" href="assets/css/main.css">
   <link rel="stylesheet" href="assets/css/util.css">

   <link rel="stylesheet" href="assets/fonts/font-awesome-4.7.0/css/font-awesome.min.css">

   <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
   <link rel="stylesheet" href="vendor/animate/animate.css">
   <link rel="stylesheet" href="vendor/css-hamburgers/hamburgers.min.css">
   <link rel="stylesheet" href="vendor/select2/select2.min.css">
   <style>
      /* alert disesuaikan dengan dark theme */
      .alert-box {
         background: #c80000;
         color: white;
         padding: 12px;
         border-radius: 10px;
         margin-bottom: 15px;
         text-align: center;
         font-size: 14px;
      }

      .alert-box button {
         margin-top: 8px;
         padding: 4px 12px;
         border: none;
         background: white;
         color: #c80000;
         border-radius: 5px;
         cursor: pointer;
      }
   </style>
</head>

<body>

   <div class="limiter">
      <div class="container-login100">
         <div class="wrap-login100">

            <!-- IMAGE -->
            <div class="login100-pic js-tilt" data-tilt>
               <img src="assets/image/Logo Login.png" alt="IMG">
            </div>

            <!-- FORM -->
            <form class="login100-form validate-form" action="index.php?page=cek_login" method="post">

               <span class="login100-form-title">
                  Login Parkify
               </span>

               <?php
               if (isset($_GET['pesan'])) {
                  $message = "";

                  if ($_GET['pesan'] == "user_tidak_ada") {
                     $message = "Username tidak ditemukan!";
                  } elseif ($_GET['pesan'] == "password_salah") {
                     $message = "Password salah!";
                  } elseif ($_GET['pesan'] == "gagal") {
                     $message = "Username dan Password wajib diisi!";
                  }

                  if ($message != "") {
                     echo "
						<div id='loginAlert' class='alert-box'>
							<span>$message</span><br>
							<button type='button' onclick='closeAlert()'>OK</button>
						</div>
						";
                  }
               }
               ?>

               <!-- USERNAME -->
               <div class="wrap-input100 validate-input" data-validate="Username wajib diisi">
                  <input class="input100" type="text" name="username" placeholder="Username" required>
                  <span class="focus-input100"></span>
                  <span class="symbol-input100">
                     <i class="fa fa-user"></i>
                  </span>
               </div>

               <!-- PASSWORD -->
               <div class="wrap-input100 validate-input" data-validate="Password wajib diisi">
                  <input class="input100" type="password" name="password" placeholder="Password" required>
                  <span class="focus-input100"></span>
                  <span class="symbol-input100">
                     <i class="fa fa-lock"></i>
                  </span>
               </div>

               <!-- BUTTON -->
               <div class="container-login100-form-btn">
                  <button class="login100-form-btn" type="submit">
                     Login
                  </button>
               </div>

               <div class="text-center p-t-12">
                  <span class="txt1">

                  </span>
                  <a class="txt2" href="#">

                  </a>
               </div>

               <div class="text-center p-t-136">
                  <a class="txt2" href="#">

                  </a>
               </div>
            </form>
         </div>
      </div>
   </div>

   <!-- JS -->
   <script src="vendor/jquery/jquery-3.2.1.min.js"></script>
   <script src="vendor/bootstrap/js/popper.js"></script>
   <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
   <script src="vendor/select2/select2.min.js"></script>
   <script src="vendor/tilt/tilt.jquery.min.js"></script>

   <script>
      $('.js-tilt').tilt({
         scale: 1.1
      });

      function closeAlert() {
         let alertBox = document.getElementById("loginAlert");
         if (alertBox) {
            alertBox.style.display = "none";
         }
      }
   </script>

</body>

</html>