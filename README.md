# PHP Assessment Project

This is a simple PHP-based assessment project designed for educational purposes. The project includes two roles: Teacher and Student. Teachers can create and manage quizzes, while students can take quizzes and view their results.

## Features

### Teacher Role
- **Create Quizzes**: Teachers can add new quizzes with "Yes" or "No" questions.
- **Edit Quizzes**: Teachers can modify existing quizzes by adding, editing, or deleting questions.
- **View Results**: Teachers can view the results of quizzes taken by students, including individual scores and overall analytics.

### Student Role
- **View Quizzes**: Students can view available quizzes.
- **Take Quizzes**: Students can answer "Yes" or "No" questions in the quizzes.
- **View Results**: After submitting a quiz, students can view their scores.

## Installation

Follow these steps to set up the project on your local machine:

1. **Clone the Repository**

   ```bash
   git clone https://github.com/yourusername/php-assessment-project.git
   cd php-assessment-project
   ```

2. **Setup the Database**

   - Create a new MySQL database (e.g., `assessment_db`).
   - Import the SQL schema provided in `database.sql` into your MySQL database.
   
   ```sql
   CREATE DATABASE assessment_db;
   USE assessment_db;
   SOURCE /path/to/database.sql;
   ```

3. **Configure Database Connection**

   - Open `db.php` file.
   - Update the database connection details with your database credentials:

   ```php
   <?php
   $host = 'localhost';    // Database host
   $user = 'root';         // Database username
   $pass = '';             // Database password
   $dbname = 'assessment_db'; // Database name

   $conn = new mysqli($host, $user, $pass, $dbname);

   if ($conn->connect_error) {
       die("Connection failed: " . $conn->connect_error);
   }
   ?>
   ```

4. **Start the Local Server**

   - Use the built-in PHP server or a local server like XAMPP, WAMP, or MAMP.

   ```bash
   php -S localhost:8000
   ```

   Or, if you are using XAMPP, place the project folder in the `htdocs` directory.

5. **Access the Application**

   Open a web browser and navigate to `http://localhost:8000` (or `http://localhost/php-assessment-project` if using XAMPP).

6. **Login with Default Credentials**

   Use the following default credentials to log in:

   - **Teacher**: 
     - Username: `teacher1`
     - Password: `password`
   
   - **Student**:
     - Username: `student1`
     - Password: `password`

## Usage

### Teacher Actions

1. **Log in** as a teacher to create and manage quizzes.
2. **Create a Quiz**: Navigate to `create_quiz.php` to add a new quiz.
3. **Edit a Quiz**: Go to `edit_quiz.php` to modify questions.
4. **View Results**: Check `view_results.php` to see students' performance and quiz analytics.

### Student Actions

1. **Log in** as a student to view and take quizzes.
2. **Take a Quiz**: Navigate to `student.php` to select and complete quizzes.
3. **View Results**: View your quiz results after submission.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Contributing

Contributions are welcome! Please fork the repository and submit a pull request for any changes.

## Contact

For any questions or feedback, please contact `syedasgarahmed11@gmail.com`.
