<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div id="imported-header"></div>
    <script>
        fetch("/frontend/header/header.html")
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }
                return response.text();
            })
            .then((html) => {
                document.getElementById("imported-header").innerHTML = html;
            })
            .catch((error) => {
                console.error("There was a problem fetching the HTML:", error);
            });
    </script>
    <section class="text-gray-600 body-font">
        <div class="container px-5 py-24 mx-auto flex flex-wrap items-center">
            <div class="lg:w-3/5 md:w-1/2 md:pr-16 lg:pr-0 pr-0">
                <h1 class="title-font text-4xl text-blue-500 font-bold">
                    Book Services
                </h1>
                <p class="leading-relaxed mt-4">
                    Connect with us to get its full potential.
                </p>
            </div>
            <div class="lg:w-2/6 md:w-1/2 bg-gray-100 rounded-lg p-8 flex flex-col md:ml-auto w-full mt-10 md:mt-0">
                <h2 class="text-gray-900 text-lg font-medium title-font mb-5">
                    Login
                </h2>
                <form method="post">
                    <div class="relative mb-4">
                        <label for="full-name" class="leading-7 text-sm text-gray-600">Email or phone number</label>
                        <input type="text" id="full-name" name="email" class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out" />
                    </div>
                    <div class="relative mb-4">
                        <label for="email" class="leading-7 text-sm text-gray-600">Password</label>
                        <input type="password" id="password" name="password" class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out" />
                    </div>
                    <input type="submit" value="Login" name="submit" class="text-white bg-blue-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg font-bold">
                    <div class="text-xs text-gray-500 mt-3 flex items-center justify-center">
                        <p class="inline-block text-blue-600">Forgot password?</p>
                    </div>
                    <hr class="border-gray-600 my-4" />
                    <button class="text-white bg-blue-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg font-bold" name='login'>
                        Create a new Account
                    </button>
                </form>
            </div>
        </div>
    </section>
    <?php
    $conn = mysqli_connect("localhost", "root", "", "library");
    $UserRole = "";
    $email = 'me';
    $password = 'me';
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    if(isset($_POST['login'])){
        $email = $_POST['email'];
        $password = $_POST['password'];
        $sql = "SELECT * FROM users WHERE email = '$email' AND password = 'XXXXXXXXX'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            // output data of each row
            while($row = mysqli_fetch_assoc($result)) {
                $UserRole = $row["role"];
                $email = $row["email"];
                $password = $row["password"];
            }
        } else {
            echo "0 results";
        }
    }
    ?>
</body>
</html>
