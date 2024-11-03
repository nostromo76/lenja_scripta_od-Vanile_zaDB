<?php
class DatabaseSeeder {
    private $db;

    public function __construct() {
        $this->db = @mysqli_connect("localhost", "root", "", "cvecara");
        if (mysqli_connect_error()) {
            die("Database connection failed: " . mysqli_connect_error());
        }
        mysqli_query($this->db, "SET NAMES UTF8");
    }

    public function seedData($data) {
        $sql = "INSERT INTO shop (kategorija, cena, naziv, opis, slika, istaknuto, brisan) VALUES ";
        $insertValues = [];

        foreach ($data as $item) {
            $insertValues[] = "('" . mysqli_real_escape_string($this->db, $item['kategorija']) . "', " .
                $item['cena'] . ", '" .
                mysqli_real_escape_string($this->db, $item['naziv']) . "', '" .
                mysqli_real_escape_string($this->db, $item['opis']) . "', '" .
                mysqli_real_escape_string($this->db, $item['slika']) . "', " .
                $item['istaknuto'] . ", " .
                $item['brisan'] . ")";
        }

        $sql .= implode(", ", $insertValues);

        if (mysqli_query($this->db, $sql) === FALSE) {
            echo "Error: " . mysqli_error($this->db);
        } else {
            echo "Podaci uspesno ubaceni u bazu.";
        }
    }

    public function insertSingle($item) {
        $stmt = $this->db->prepare("INSERT INTO artikli (kategorija, cena, naziv, opis, slika, istaknuto, brisan) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sdssssi", $item['kategorija'], $item['cena'], $item['naziv'], $item['opis'], $item['slika'], $item['istaknuto'], $item['brisan']);
        $stmt->execute();

        if ($stmt->error) {
            echo "Error u toku upisa: " . $stmt->error;
        } else {
            echo "Pojedinacni unos uspesno izvrsen.";
        }
    }

    public function close() {
        mysqli_close($this->db);
    }
}

// forma  ako ide bulk
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $seeder = new DatabaseSeeder();

    if (isset($_POST['bulk_insert'])) {
        // Bull metoda 
        $data = [];
        for ($i = 1; $i <= 100; $i++) {
            $data[] = [
                'kategorija' => 'Category' . rand(1, 5),
                'cena' => round(rand(100, 500) + rand(0, 99) / 100, 2),
                'naziv' => 'Product' . $i,
                'opis' => 'Description for product ' . $i,
                'slika' => 'image' . rand(1, 10) . '.jpg',
                'istaknuto' => rand(0, 1),
                'brisan' => 0
            ];
        }
        $seeder->seedData($data);
    } else {
        // pojedinacan unos
        $item = [
            'kategorija' => $_POST['kategorija'],
            'cena' => $_POST['cena'],
            'naziv' => $_POST['naziv'],
            'opis' => $_POST['opis'],
            'slika' => '', // ovde moze da se ubaci  putanja do slika 
            'istaknuto' => $_POST['istaknuto'],
            'brisan' => $_POST['brisan']
        ];

        // unos slika
        if (isset($_FILES['slika']) && $_FILES['slika']['error'] == UPLOAD_ERR_OK) {
            $uploadsDir = 'uploads/'; // direktorijum odakle  se vrsi upload
            $tmpName = $_FILES['slika']['tmp_name'];
            $fileName = basename($_FILES['slika']['name']);
            $filePath = $uploadsDir . $fileName;

            // umetanje slika u zeljeni direktorijum ako se radi vanila bez baze 
            if (move_uploaded_file($tmpName, $filePath)) {
                $item['slika'] = $filePath;
            } else {
                echo "Unos neuspesan.";
            }
        } else {
            echo "Nije uneta slika ili tokom umetanja se desila  greska ";
        }

        $seeder->insertSingle($item);
    }
    $seeder->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seeder za Bazu</title>
</head>
<body>
    <h1>Unos podataka u Bazu</h1>

    <form action="" method="POST" enctype="multipart/form-data">
        <h2>Add Single Product</h2>
        <label for="kategorija">Kategorija:</label>
        <input type="text" id="kategorija" name="kategorija" required><br>

        <label for="cena">Cena:</label>
        <input type="number" id="cena" name="cena" step="0.01" required><br>

        <label for="naziv">Ime:</label>
        <input type="text" id="naziv" name="naziv" required><br>

        <label for="opis">Opis:</label>
        <textarea id="opis" name="opis" required></textarea><br>

        <label for="slika">Slika:</label>
        <input type="file" id="slika" name="slika" accept="image/*" required><br>

        <label for="istaknuto">Izmenjeno:</label>
        <select id="istaknuto" name="istaknuto">
            <option value="1">Da</option>
            <option value="0">Ne</option>
        </select><br>

        <label for="brisan"Obrisano>:</label>
        <select id="brisan" name="brisan">
            <option value="1">Da</option>
            <option value="0">Ne</option>
        </select><br>

        <button type="submit">Dodaj item</button>
    </form>

    <form action="" method="POST">
        <h2>Bulk ubacivanje do 100 itema/h2>
        <button type="submit" name="bulk_insert">Ubaci Bulk Data</button>
    </form>
</body>
</html>