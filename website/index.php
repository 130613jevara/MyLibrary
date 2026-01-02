<?php
require_once 'config.php';

// Handle book deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: index.php");
    exit;
}

// Handle book editing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_book'])) {
    $id = intval($_POST['id']);
    $title = htmlspecialchars(trim($_POST['title']));
    $author_id = intval($_POST['author_id']);
    $year = intval($_POST['year']);
    $genre_id = intval($_POST['genre_id']);
    $description = htmlspecialchars(trim($_POST['description']));
    
    if (!empty($title) && $author_id > 0) {
        $stmt = $conn->prepare("UPDATE books SET title = ?, author_id = ?, year = ?, genre_id = ?, description = ? WHERE id = ?");
        $stmt->execute([$title, $author_id, $year, $genre_id, $description, $id]);
        $success = "Book updated successfully!";
    }
}

// Handle book submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_book'])) {
    $title = htmlspecialchars(trim($_POST['title']));
    $author_id = intval($_POST['author_id']);
    $year = intval($_POST['year']);
    $genre_id = intval($_POST['genre_id']);
    $description = htmlspecialchars(trim($_POST['description']));
    $added_by_user_id = intval($_POST['added_by_user_id']);
    
    if (!empty($title) && $author_id > 0) {
        $stmt = $conn->prepare("INSERT INTO books (title, author_id, year, genre_id, description, added_by_user_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $author_id, $year, $genre_id, $description, $added_by_user_id]);
        $success = "Book added successfully!";
    }
}

// Get all authors for dropdown
$authorsStmt = $conn->query("SELECT id, name FROM authors ORDER BY name ASC");
$authors = $authorsStmt->fetchAll(PDO::FETCH_ASSOC);

// Get all genres for dropdown
$genresStmt = $conn->query("SELECT id, name FROM genres ORDER BY name ASC");
$genres = $genresStmt->fetchAll(PDO::FETCH_ASSOC);

// Get all users for dropdown
$usersStmt = $conn->query("SELECT id, username, full_name FROM users ORDER BY username ASC");
$users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);

// Get book for editing
$editBook = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$id]);
    $editBook = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get search term
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Fetch books with JOIN to get author and genre names
if (!empty($search)) {
    $stmt = $conn->prepare("
        SELECT b.*, a.name as author_name, g.name as genre_name, u.username, u.full_name
        FROM books b
        LEFT JOIN authors a ON b.author_id = a.id
        LEFT JOIN genres g ON b.genre_id = g.id
        LEFT JOIN users u ON b.added_by_user_id = u.id
        WHERE b.title LIKE ? OR a.name LIKE ?
        ORDER BY b.date_added DESC
    ");
    $searchTerm = "%$search%";
    $stmt->execute([$searchTerm, $searchTerm]);
} else {
    $stmt = $conn->query("
        SELECT b.*, a.name as author_name, g.name as genre_name, u.username, u.full_name
        FROM books b
        LEFT JOIN authors a ON b.author_id = a.id
        LEFT JOIN genres g ON b.genre_id = g.id
        LEFT JOIN users u ON b.added_by_user_id = u.id
        ORDER BY b.date_added DESC
    ");
}
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Little Library ✨</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Quicksand', sans-serif;
            background: linear-gradient(135deg, #ffeef8 0%, #fff5f7 50%, #f0e8ff 100%);
            min-height: 100vh;
            color: #4a4a4a;
        }
        header {
            background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 50%, #fda085 100%);
            padding: 40px 20px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(255, 154, 158, 0.3);
            position: relative;
            overflow: hidden;
        }
        header::before {
            content: '✨';
            position: absolute;
            font-size: 40px;
            top: 20px;
            left: 10%;
            animation: float 3s ease-in-out infinite;
        }
        header::after {
            content: '🌸';
            position: absolute;
            font-size: 35px;
            top: 30px;
            right: 10%;
            animation: float 4s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 48px;
            color: white;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 10px;
        }
        header p {
            color: white;
            font-size: 18px;
            font-weight: 500;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
        }
        .search-bar {
            margin: 30px 0;
            text-align: center;
        }
        .search-bar input {
            padding: 15px 20px;
            width: 350px;
            max-width: 90%;
            border: 3px solid #ffd1dc;
            border-radius: 30px;
            font-family: 'Quicksand', sans-serif;
            font-size: 16px;
            transition: all 0.3s ease;
            background: white;
        }
        .search-bar input:focus {
            outline: none;
            border-color: #ff9a9e;
            box-shadow: 0 0 15px rgba(255, 154, 158, 0.3);
        }
        .search-bar button {
            padding: 15px 30px;
            background: linear-gradient(135deg, #ff9a9e, #fecfef);
            color: white;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-family: 'Quicksand', sans-serif;
            font-weight: 600;
            font-size: 16px;
            margin-left: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 154, 158, 0.3);
        }
        .search-bar button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 154, 158, 0.4);
        }
        .add-book-section {
            background: white;
            padding: 35px;
            margin: 30px 0;
            border-radius: 25px;
            box-shadow: 0 8px 30px rgba(255, 154, 158, 0.15);
            border: 3px solid #ffd1dc;
        }
        .add-book-section h2 {
            font-family: 'Playfair Display', serif;
            color: #ff6b9d;
            margin-bottom: 25px;
            font-size: 32px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #ff6b9d;
            font-size: 15px;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 18px;
            border: 2px solid #ffd1dc;
            border-radius: 15px;
            font-family: 'Quicksand', sans-serif;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #fffbfc;
        }
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #ff9a9e;
            background: white;
            box-shadow: 0 0 10px rgba(255, 154, 158, 0.2);
        }
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        .submit-btn {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            color: #ff6b9d;
            padding: 15px 40px;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-size: 18px;
            font-weight: 700;
            font-family: 'Quicksand', sans-serif;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(168, 237, 234, 0.3);
        }
        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 25px rgba(168, 237, 234, 0.5);
        }
        .cancel-btn {
            background: linear-gradient(135deg, #ffd1dc 0%, #ffb6c1 100%);
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-size: 18px;
            font-weight: 700;
            font-family: 'Quicksand', sans-serif;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 182, 193, 0.3);
            margin-left: 10px;
            text-decoration: none;
            display: inline-block;
        }
        .cancel-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 25px rgba(255, 182, 193, 0.5);
        }
        .success {
            background: linear-gradient(135deg, #d4fc79 0%, #96e6a1 100%);
            color: #2d5016;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 15px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(150, 230, 161, 0.3);
        }
        .books-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        .book-card {
            background: white;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(255, 154, 158, 0.15);
            transition: all 0.3s ease;
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }
        .book-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #ff9a9e, #fecfef, #fda085);
        }
        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 35px rgba(255, 154, 158, 0.25);
            border-color: #ffd1dc;
        }
        .book-card h3 {
            font-family: 'Playfair Display', serif;
            color: #ff6b9d;
            margin-bottom: 12px;
            font-size: 24px;
            line-height: 1.3;
        }
        .book-card .author {
            color: #9b59b6;
            font-style: italic;
            margin-bottom: 12px;
            font-weight: 600;
            font-size: 16px;
        }
        .book-card .meta {
            font-size: 14px;
            color: #a29bfe;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .book-card .description {
            margin-top: 15px;
            line-height: 1.7;
            color: #666;
            font-size: 15px;
        }
        .book-card .added-by {
            margin-top: 15px;
            font-size: 13px;
            color: #ffb6c1;
            font-weight: 600;
            padding-top: 15px;
            border-top: 2px solid #fff0f5;
        }
        .book-actions {
            margin-top: 15px;
            display: flex;
            gap: 10px;
        }
        .edit-btn, .delete-btn {
            padding: 8px 20px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-family: 'Quicksand', sans-serif;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        .edit-btn {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            color: #ff6b9d;
        }
        .delete-btn {
            background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
            color: white;
        }
        .edit-btn:hover, .delete-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 154, 158, 0.3);
        }
        .book-count {
            margin-top: 50px;
            font-size: 28px;
            font-weight: 700;
            font-family: 'Playfair Display', serif;
            color: #ff6b9d;
            text-align: center;
        }
        .emoji-decoration {
            display: inline-block;
            margin: 0 8px;
        }
    </style>
</head>
<body>
    <header>
        <h1>✨ My Little Library 💕</h1>
        <p>where bookworms bloom and stories sparkle</p>
    </header>

    <div class="container">
        <!-- Search Bar -->
        <div class="search-bar">
            <form method="GET" action="">
                <input type="text" name="search" placeholder="search for magical books... 🔍" value="<?= htmlspecialchars($search) ?>">
                <button type="submit">Search</button>
                <?php if (!empty($search)): ?>
                    <a href="index.php"><button type="button">Clear</button></a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Add/Edit Book Section -->
        <div class="add-book-section">
            <h2><?= $editBook ? '✏️ Edit Book' : '✨ Add Your Favorite Book' ?></h2>
            <?php if (isset($success)): ?>
                <div class="success">✨ Yay! <?= $success ?> 💕</div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <?php if ($editBook): ?>
                    <input type="hidden" name="id" value="<?= $editBook['id'] ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="title">📖 Book Title *</label>
                    <input type="text" id="title" name="title" required placeholder="e.g., Little Women" value="<?= $editBook ? htmlspecialchars($editBook['title']) : '' ?>">
                </div>
                
                <div class="form-group">
                    <label for="author_id">✍️ Author *</label>
                    <select id="author_id" name="author_id" required>
                        <option value="">Select an author...</option>
                        <?php foreach ($authors as $author): ?>
                            <option value="<?= $author['id'] ?>" <?= ($editBook && $editBook['author_id'] == $author['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($author['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="year">📅 Year Published</label>
                    <input type="number" id="year" name="year" min="1000" max="2100" placeholder="e.g., 1868" value="<?= $editBook ? $editBook['year'] : '' ?>">
                </div>
                
                <div class="form-group">
                    <label for="genre_id">🌸 Genre</label>
                    <select id="genre_id" name="genre_id">
                        <option value="">Select a genre...</option>
                        <?php foreach ($genres as $genre): ?>
                            <option value="<?= $genre['id'] ?>" <?= ($editBook && $editBook['genre_id'] == $genre['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($genre['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="description">💭 What's it about?</label>
                    <textarea id="description" name="description" placeholder="Share what makes this book special..."><?= $editBook ? htmlspecialchars($editBook['description']) : '' ?></textarea>
                </div>
                
                <?php if (!$editBook): ?>
                <div class="form-group">
                    <label for="added_by_user_id">💌 Added By *</label>
                    <select id="added_by_user_id" name="added_by_user_id" required>
                        <option value="">Select user...</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $user['id'] ?>">
                                <?= htmlspecialchars($user['full_name'] ?: $user['username']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                
                <button type="submit" name="<?= $editBook ? 'edit_book' : 'add_book' ?>" class="submit-btn">
                    <?= $editBook ? 'Update Book ✏️' : 'Add to Library ✨' ?>
                </button>
                <?php if ($editBook): ?>
                    <a href="index.php" class="cancel-btn">Cancel</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Books Display -->
        <h2 class="book-count">
            <span class="emoji-decoration">📚</span>
            <?= !empty($search) ? "Search Results for '$search'" : "Our Collection" ?> 
            <span class="emoji-decoration">(<?= count($books) ?> books)</span>
            <span class="emoji-decoration">🌷</span>
        </h2>
        
        <div class="books-grid">
            <?php foreach ($books as $book): ?>
                <div class="book-card">
                    <h3><?= htmlspecialchars($book['title']) ?></h3>
                    <div class="author">by <?= htmlspecialchars($book['author_name'] ?: 'Unknown Author') ?></div>
                    <div class="meta">
                        <?php if ($book['year']): ?>
                            <span><?= htmlspecialchars($book['year']) ?></span>
                        <?php endif; ?>
                        <?php if ($book['genre_name']): ?>
                            <span> • <?= htmlspecialchars($book['genre_name']) ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if ($book['description']): ?>
                        <div class="description"><?= htmlspecialchars($book['description']) ?></div>
                    <?php endif; ?>
                    <div class="added-by">
                        💕 Added by <?= htmlspecialchars($book['full_name'] ?: $book['username'] ?: 'Unknown User') ?> 
                        on <?= date('M d, Y', strtotime($book['date_added'])) ?>
                    </div>
                    <div class="book-actions">
                        <a href="?edit=<?= $book['id'] ?>" class="edit-btn">✏️ Edit</a>
                        <a href="?delete=<?= $book['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this book? 🥺')">🗑️ Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($books)): ?>
            <p style="text-align: center; margin-top: 40px; color: #ffb6c1; font-size: 18px;">No books found yet... be the first to add one! 🌸</p>
        <?php endif; ?>
    </div>
</body>
</html>