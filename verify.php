if (password_verify($entered_password, $stored_hashed_password)) {
    // Password is correct, log in the user
} else {
    // Incorrect password
}
