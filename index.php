<?php
$conn = mysqli_connect("localhost", "root", "", "library");
$UserRole = "";

$email = 'me';
$password = 'me';

// Uncomment and use the functions as needed
// echo(signUp($email, $password));
// echo(login($email, $password));
// echo(addBook('book1', 'author1', 'genre1', 10));
// echo(getBookInformation(1)['title']);
// echo(makeReservation(1, 1));
// echo(issueBook(1, 1));
// echo(provideFeedback(1, 1, 5, 'good book'));


if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

function login($email, $password)
{
    global $conn, $UserRole;
    
    
    // Retrieve hashed password from the database
    $sql = "SELECT * FROM User WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $hashedPassword = $user['password']; // Get hashed password from the database

        // Verify provided password with hashed password from the database
        if (password_verify($password, $hashedPassword)) {
            $UserRole = $user['role']; // Set the user role
            return true;
        } else {
            return false; // Passwords do not match
        }
    } else {
        return false; // User not found
    }
}

function signUp($email, $password)
{
    global $conn;
    
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Using bcrypt for password hashing
    $sql = "INSERT INTO User (email, password) VALUES ('$email', '$hashedPassword')";
    $result = mysqli_query($conn, $sql);
    if (mysqli_affected_rows($conn) > 0) {
        return true;
    } else {
        return false;
    }
}

function deleteUser($email)
{
    global $conn;
    if (hasIssuedBooks($email)) {
        return false;
    }
    $sql = "DELETE FROM User WHERE email='$email';";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        return true;
    } else {
        return false;
    }
}

function getBookInformation($bookId)
{
    global $conn;
    $sql = "SELECT * FROM Book WHERE id='$bookId';";
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $book = mysqli_fetch_assoc($result);
        return $book;
    } else {
        return false;
    }
}

function makeReservation($userId, $bookId)
{
    global $conn;
    $existingReservationSql = "SELECT * FROM Reservation WHERE bookId=?";
    $stmt = mysqli_prepare($conn, $existingReservationSql);
    if (!$stmt) {
        echo "Error preparing statement: " . mysqli_error($conn);
        return false;
    }
    mysqli_stmt_bind_param($stmt, "i", $bookId);
    mysqli_stmt_execute($stmt);
    $existingReservationResult = mysqli_stmt_get_result($stmt);
    if ($existingReservationResult && mysqli_num_rows($existingReservationResult) > 0) {
        return false;
    }
    $sql = "INSERT INTO Reservation (userId, bookId) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        echo "Error preparing statement: " . mysqli_error($conn);
        return false;
    }
    mysqli_stmt_bind_param($stmt, "ii", $userId, $bookId);
    $result = mysqli_stmt_execute($stmt);
    if ($result) {
        $updateBookStatusSql = "UPDATE Book SET status='RESERVED' WHERE id=?";
        $stmt = mysqli_prepare($conn, $updateBookStatusSql);
        if (!$stmt) {
            echo "Error preparing statement: " . mysqli_error($conn);
            return false;
        }
        mysqli_stmt_bind_param($stmt, "i", $bookId);
        $result = mysqli_stmt_execute($stmt);
        if ($result) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function provideback($userId, $bookId, $rating, $comment)
{
    global $conn;
    
    // Check if the user has already provided feedback for the book
    $existingFeedbackSql = "SELECT * FROM FeedBack WHERE userId = ? AND bookId = ?";
    $stmt = mysqli_prepare($conn, $existingFeedbackSql);
    if (!$stmt) {
        echo "Error preparing statement: " . mysqli_error($conn);
        return false;
    }
    mysqli_stmt_bind_param($stmt, "ii", $userId, $bookId);
    mysqli_stmt_execute($stmt);
    $existingFeedbackResult = mysqli_stmt_get_result($stmt);
    if ($existingFeedbackResult && mysqli_num_rows($existingFeedbackResult) > 0) {
        echo "User has already provided feedback for this book";
        return false;
    }
    
    // Proceed with providing feedback if the user hasn't already provided feedback
    $sql = "INSERT INTO FeedBack (userId, bookId, rating, comment) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        echo "Error preparing statement: " . mysqli_error($conn);
        return false;
    }
    mysqli_stmt_bind_param($stmt, "iiis", $userId, $bookId, $rating, $comment);
    $result = mysqli_stmt_execute($stmt);
    if ($result) {
        return true;
    } else {
        echo "Error providing feedback: " . mysqli_error($conn);
        return false;
    }
}

function addBook($title, $author, $genre, $quantity)
{
    global $conn;
    $sql = "INSERT INTO Book (title, author, genre, Quantity) VALUES ('$title', '$author', '$genre', '$quantity')";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        return true;
    } else {
        return false;
    }
}

function hasIssuedBooks($userId)
{
    global $conn;
    $bookSQL = "SELECT * FROM Book WHERE takenById='$userId';";
    $bookquery = mysqli_query($conn, $bookSQL);
    if ($bookquery) { // Check if the query was successful
        $hasbook = mysqli_num_rows($bookquery);
        if ($hasbook > 0) {
            return true;
        } else {
            return false;
        }
    } else {
        echo "Error executing query: " . mysqli_error($conn);
        return false;
    }
}

function issueBook($userId, $bookId)
{
    global $conn;
    
    // Check if the book is already issued to the user
    $existingIssueSql = "SELECT * FROM issuedBooks WHERE userId = ? AND bookId = ?";
    $stmt = mysqli_prepare($conn, $existingIssueSql);
    if (!$stmt) {
        echo "Error preparing statement: " . mysqli_error($conn);
        return false;
    }
    mysqli_stmt_bind_param($stmt, "ii", $userId, $bookId);
    mysqli_stmt_execute($stmt);
    $existingIssueResult = mysqli_stmt_get_result($stmt);
    if ($existingIssueResult && mysqli_num_rows($existingIssueResult) > 0) {
        echo "Book is already issued to the user";
        return false;
    }
    
    // Proceed with issuing the book if it's available
    $bookInfo = getBookInformation($bookId);
    if (!$bookInfo || $bookInfo['Quantity'] < 1) {
        echo "Book not available";
        return false;
    }

    // Insert record into issuedBooks table
    $sql = "INSERT INTO issuedBooks (userId, bookId) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        echo "Error preparing statement: " . mysqli_error($conn);
        return false;
    }
    mysqli_stmt_bind_param($stmt, "ii", $userId, $bookId);
    $result = mysqli_stmt_execute($stmt);
    if ($result) {
        // Update quantity of the book
        $updateQuantitySql = "UPDATE Book SET Quantity = Quantity - 1 WHERE id = ?";
        $stmt = mysqli_prepare($conn, $updateQuantitySql);
        if (!$stmt) {
            echo "Error preparing statement: " . mysqli_error($conn);
            return false;
        }
        mysqli_stmt_bind_param($stmt, "i", $bookId);
        $result = mysqli_stmt_execute($stmt);
        
        if ($result) {
            return true;
        } else {
            echo "Error updating book quantity: " . mysqli_error($conn);
            return false;
        }
    } else {
        echo "Error issuing book: " . mysqli_error($conn);
        return false;
    }
}
function getTotalBooks(){
    global $conn;
    $sql = "SELECT * FROM Book";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        return mysqli_num_rows($result);
    } else {
        return 0;
    }

}
function getAvailableBooks(){
    global $conn;
    $sql = "SELECT * FROM Book WHERE status='AVILAIBLE'";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        return mysqli_num_rows($result);
    } else {
        return 0;
    }
}
function getBorrowedBooks(){
    global $conn;
    $sql = "SELECT * FROM Book WHERE status='TAKEN'";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        return mysqli_num_rows($result);
    } else {
        return 0;
    }
}
function editBook($bookId, $bookName, $author, $genre, $quantity)
{
    global $conn;

    // Update the book information in the database
    $sql = "UPDATE Book SET title='$bookName', author='$author', genre='$genre', Quantity='$quantity' WHERE id='$bookId'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        return true;
    } else {
        return false;
    }
}
function deleteBook($id){
    global $conn;
    
    // First, check if the book exists
    $bookExistsSql = "SELECT * FROM Book WHERE id='$id'";
    $result = mysqli_query($conn, $bookExistsSql);
    if ($result && mysqli_num_rows($result) > 0) {
        // Book exists, proceed with deletion
        $deleteBookSql = "DELETE FROM Book WHERE id='$id'";
        $deleteResult = mysqli_query($conn, $deleteBookSql);
        if ($deleteResult) {
            return true; // Book deleted successfully
        } else {
            return false; // Failed to delete book
        }
    } else {
        return false; // Book does not exist
    }
}
function loopGOAround(){
    global $conn;
    $count=0;
    $sql = "SELECT * FROM Book";
    $result = mysqli_query($conn, $sql);
    if($result){
    //   loop through all the id's
    while($row = mysqli_fetch_assoc($result)){
        $count++;
    }
    }
}


?>