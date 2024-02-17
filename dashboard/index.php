<?php
include '../index.php';

if(isset($_POST['addbookbtn'])){
    $title = $_POST['title'];
    $author = $_POST['author'];
    $genre = $_POST['genre'];
    $quantity = $_POST['quantity'];
    $imgData = file_get_contents($_FILES['cover']['tmp_name']);
    $imgType = $_FILES['cover']['type'];
   $conn= mysqli_connect('localhost',"root","","library");
    $sql = "INSERT INTO book (title, author, genre, Quantity, cover, coverType) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        echo "Error preparing statement: " . mysqli_error($conn);
        return false;
    }
    mysqli_stmt_bind_param($stmt, "ssssss", $title, $author, $genre, $quantity, $imgData, $imgType); // 'b' for BLOB data
    $result = mysqli_stmt_execute($stmt);
    if ($result) {
       echo"<script>alert('Book added successfully')</script>";
       
    } else {
        echo "Error adding book: " . mysqli_error($conn);
    }
}

// The rest of your PHP code for editing and deleting books remains unchanged

if(isset($_POST['editBookBtn'])){
    $bookId = $_POST['editBookId'];
    $bookName = $_POST['editTitle'];
    $author = $_POST['editAuthor'];
    $genre = $_POST['editGenre'];
    $quantity = $_POST['editQuantity'];

    $result = editBook($bookId, $bookName, $author, $genre, $quantity,);
    if ($result) {
        echo "<script>alert('Book updated successfully')</script>";
    } else {
        echo "<script>alert('Error updating book')</script>";
    }
}

if(isset($_POST['deleteBookBtn'])){
    $id = $_POST['deleteBookId'];
    $result = deleteBook($id);
    if ($result) {
        echo "<script>alert('Book deleted successfully')</script>";
    } else {
        echo "<script>alert('Error deleting book')</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Library Admin Dashboard</title>

    <!-- External Dependencies -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css">
    <link rel="stylesheet" href="https://unpkg.com/tailwindcss@2.2.19/dist/tailwind.min.css" />
</head>

<body class="bg-gray-100 font-sans leading-normal tracking-normal">

    <!-- Header -->
    <header>
        <nav aria-label="menu nav" class="bg-gray-800 py-4">
            <div class="container mx-auto flex items-center justify-between px-4">
                <div class="text-white">
                    <h1 class="text-xl font-bold">Library Admin Dashboard</h1>
                </div>
                <div class="flex items-center">
                    <div class="text-white mr-4">
                        <span>Welcome, Admin</span>
                    </div>
                    <div class="flex items-center">
                        <!-- Add Record Button -->
                        <button id="addRecordBtn" onclick="toggleAddRecordSection()" class="text-white py-2 px-4 border border-white rounded-md mr-2">
                            Add Record
                        </button>

                        <!-- Edit Record Button -->
                        <!-- <button id="editRecordBtn" onclick="toggleEditRecordSection()" class="text-white py-2 px-4 border border-white rounded-md mr-2">
                            Edit Record
                        </button>
                        <!-- Delete Record Button -->
                        <!-- <button id="deleteRecordBtn" onclick="toggleDeleteRecordSection()" class="text-white py-2 px-4 border border-white rounded-md mr-2">
                            Delete Record
                        </button> --> 
                        <!-- Dropdown for user actions -->
                        <div class="relative">
                            <button onmouseover="toggleDD('adminDropdown')"
                                class="text-white py-2 px-4 border border-white rounded-md">
                                <i class="fas fa-user-circle"></i>
                            </button>
                            <div id="adminDropdown"
                                class="dropdownlist absolute bg-gray-800 text-white right-0 mt-3 p-3 hidden">
                                <a href="#" class="block">Profile</a>
                                <a href="#" class="block">Settings</a>
                                <a href="#" class="block">Log Out</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto mt-8">

        <!-- Add Record Form (Initially Hidden) -->
        <section id="addRecordSection" class="hidden mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold mb-4">Add Record</h2>
                <form id="addRecordForm" class="w-full max-w-lg" method="POST" enctype="multipart/form-data">
                    <!-- Add your form fields here -->
                    <div class="flex items-center mb-4">
                        <label class="w-1/4">Title:</label>
                        <input type="text" name="title" class="shadow appearance-none border rounded w-3/4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="flex items-center mb-4">
                        <label class="w-1/4">Author:</label>
                        <input type="text" name="author" class="shadow appearance-none border rounded w-3/4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="flex items-center mb-4">
                        <label class="w-1/4">Genre:</label>
                        <input type="text" name="genre" class="shadow appearance-none border rounded w-3/4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="flex items-center mb-4">
                        <label class="w-1/4">Quantity:</label>
                        <input type="number" name="quantity" class="shadow appearance-none border rounded w-3/4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="flex items-center mb-4">
                  <label class="w-1/4">Cover Image:</label>
                  <input type="file" name="cover" accept="image/*" class="shadow appearance-none border rounded w-3/4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">

                </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" name='addbookbtn'>Submit</button>
                    </div>
                </form>
            </div>
        </section>

        <!-- Edit Book Form (Initially Hidden) -->
        <section id="editRecordSection" class="hidden mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold mb-4">Edit Record</h2>
                <form id="editRecordForm" class="w-full max-w-lg" method='POST'>
                    <input type="hidden" id="editBookId" name="editBookId">
                    <div class="flex items-center mb-4">
                        <label class="w-1/4">Title:</label>
                        <input type="text" id="editTitle" name="editTitle" class="shadow appearance-none border rounded w-3/4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="flex items-center mb-4">
                        <label class="w-1/4">Author:</label>
                        <input type="text" id="editAuthor" name="editAuthor" class="shadow appearance-none border rounded w-3/4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="flex items-center mb-4">
                        <label class="w-1/4">Genre:</label>
                        <input type="text" id="editGenre" name="editGenre" class="shadow appearance-none border rounded w-3/4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="flex items-center mb-4">
                        <label class="w-1/4">Quantity:</label>
                        <input type="number" id="editQuantity" name="editQuantity" class="shadow appearance-none border rounded w-3/4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" name='editBookBtn'>Submit</button>
                    </div>
                </form>
            </div>
        </section>

        <!-- Delete Book Form (Initially Hidden) -->
        <section id="deleteRecordSection" class="hidden mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold mb-4">Delete Record</h2>
                <form id="deleteRecordForm" class="w-full max-w-lg" method='POST'>
                    <input type="hidden" id="deleteBookId" name="deleteBookId">
                    <p class="text-red-500">Are you sure you want to delete this record?</p>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" name='deleteBookBtn'>Delete</button>
                    </div>
                </form>
            </div>
        </section>

        <!-- Statistics Section -->
        <section class="mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold mb-4">Statistics</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-blue-100 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">Total Books</h3>
                        <p class="text-3xl font-bold"><?= getTotalBooks() ?></p>
                    </div>
                    <div class="bg-green-100 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">Available Books</h3>
                        <p class="text-3xl font-bold"><?= getAvailableBooks() ?></p>
                    </div>
                    <div class="bg-yellow-100 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">Borrowed Books</h3>
                        <p class="text-3xl font-bold"><?= getBorrowedBooks() ?></p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Users Table Section -->
        <section class="mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold mb-4">Books</h2>
                <div class="overflow-x-auto">
                    <table class="table-auto w-full">
                        <thead>
                            <tr>
                                <!-- <th class="border px-4 py-2">#</th> -->
                                <th class="border px-4 py-2">Title</th>
                                <th class="border px-4 py-2">Author</th>
                                <th class="border px-4 py-2">Genre</th>
                                <th class="border px-4 py-2">Quantity</th>
                                <th class="border px-4 py-2">Status</th>
                                <th class="border px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $booksSQL = "SELECT * FROM Book";
                            $booksResult = mysqli_query($conn, $booksSQL);
                            if ($booksResult && mysqli_num_rows($booksResult) > 0) {
                                while ($book = mysqli_fetch_assoc($booksResult)) {
                                    echo "<tr>";
                                    // echo "<td class='border px-4 py-2'>" . $book['id'] . "</td>";
                                    echo "<td class='border px-4 py-2'>" . $book['title'] . "</td>";
                                    echo "<td class='border px-4 py-2'>" . $book['author'] . "</td>";
                                    echo "<td class='border px-4 py-2'>" . $book['genre'] . "</td>";
                                    echo "<td class='border px-4 py-2'>" . $book['Quantity'] . "</td>";
                                    echo "<td class='border px-4 py-2'>" . $book['status'] . "</td>";
                                    echo "<td class='border px-4 py-2'><button onclick='editRecord(" . $book['id'] . ",\"" . $book['title'] . "\",\"" . $book['author'] . "\",\"" . $book['genre'] . "\",\"" . $book['Quantity'] . "\")' class='bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded'>Edit</button>";
                                    echo "<button onclick='deleteRecord(" . $book['id'] . ")' class='bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded ml-2'>Delete</button></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td class='border px-4 py-2' colspan='7'>No records found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>

    <!-- Scripts -->
    <script>
        // Function to toggle dropdown menu visibility
        function toggleDD(id) {
            var dropdown = document.getElementById(id);
            if (dropdown.style.display === "none") {
                dropdown.style.display = "block";
            } else {
                dropdown.style.display = "none";
            }
        }

        // Function to show/hide the Add Record section
        function toggleAddRecordSection() {
            var addRecordSection = document.getElementById('addRecordSection');
            addRecordSection.classList.toggle('hidden');
        }

        // Function to show/hide the Edit Record section
        function toggleEditRecordSection() {
            var editRecordSection = document.getElementById('editRecordSection');
            editRecordSection.classList.toggle('hidden');
        }

        // Function to show/hide the Delete Record section
        function toggleDeleteRecordSection() {
            var deleteRecordSection = document.getElementById('deleteRecordSection');
            deleteRecordSection.classList.toggle('hidden');
        }

        // Function to show/hide the Edit Record section and prefill the form with data
        function editRecord(id, title, author, genre, quantity) {
            var editRecordSection = document.getElementById('editRecordSection');
            editRecordSection.classList.toggle('hidden');

            // Prefill the form fields with the book data
            document.getElementById('editBookId').value = id;
            document.getElementById('editTitle').value = title;
            document.getElementById('editAuthor').value = author;
            document.getElementById('editGenre').value = genre;
            document.getElementById('editQuantity').value = quantity;
        }

        // Function to show/hide the Delete Record section and set the book ID for deletion
        function deleteRecord(id) {
            var deleteRecordSection = document.getElementById('deleteRecordSection');
            deleteRecordSection.classList.toggle('hidden');

            // Set the book ID for deletion
            document.getElementById('deleteBookId').value = id;
        }
    </script>
</body>

</html>