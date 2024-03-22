<?php
session_start();

// Initialize session variables if they don't exist
if (!isset($_SESSION['bingo_card'])) {
    // Generate a new BINGO card
    $_SESSION['bingo_card'] = generateBingoCard();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if action is to start a new game
    if (isset($_POST['action']) && $_POST['action'] == 'new_game') {
        // Generate a new BINGO card
        $_SESSION['bingo_card'] = generateBingoCard();
        // Reset the list of called numbers
        $_SESSION['called_numbers'] = array();
    }

    // Check if action is to call a new number
    if (isset($_POST['action']) && $_POST['action'] == 'call_number') {
        // Call a random BINGO number
        $calledNumber = callRandomNumber();
        // Store the called number in the session
        $_SESSION['called_numbers'][] = $calledNumber;
        // Check if the called number is on the player's card and mark it
        markCalledNumber($_SESSION['bingo_card'], $calledNumber);
    }
}
// Function to generate a new random BINGO card
function generateBingoCard() {
    $bingoCard = array();

    $letters = array('B', 'I', 'N', 'G', 'O');

    for ($i = 0; $i < count($letters); $i++) {
        $column = array();

        while (count($column) < 5) {
            $min = $i * 15 + 1;
            $max = $min + 14;
            $number = mt_rand($min, $max);

            if (!in_array($number, $column)) {
                $column[] = $number;
            }
        }

        $bingoCard[$letters[$i]] = $column;
    }

    return $bingoCard;
}

// Function to generate a random number
function callRandomNumber() {
    return rand(1, 75);
}

// Function to mark a number on the BINGO card
function markCalledNumber(&$bingoCard, $number) {
    foreach ($bingoCard as $letter => &$column) {
        $key = array_search($number, $column);
        if ($key !== false) {
            $column[$key] = '<mark>' . $number . '</mark>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bingo!</title>
    
    <style>
        
    body {
        width: 80%;
        margin: auto;
        text-align: center;
    }
    div > div.called {
        text-align: center;
        font-size: 16pt;
        overflow: auto;
        max-height: 350px;
    }

    .gamebox {
        display: flex;
        flex-direction: row;
        border: 1px solid rgba(34,36,38,.15);
        border-radius: 4px;
    }
    .board table {
        color: darkgrey;
        border: 2px solid black;
        border-collapse: collapse;
        margin: auto;
    }
    
    .ui.vertical.segments {
        margin: 14px;
        border: 1px solid rgba(34,36,38,.15);
        border-radius: 4px;
        
    }
    .ui.segment {
        border: 1px solid rgba(34,36,38,.15);
        border-radius: 4px;
    }
    .board th {
        border: 3px solid black;
        background-color: darkgreen;
        color: goldenrod;
        font-weight: 900;
        font-family: sans-serif;
        aspect-ratio : 1 / 1;
        font-size:40pt;
    }

    .board td {
        border: 3px solid black;
        text-align: center;
    }

    td > div {
        font-size: 28pt;
        font-weight: 900;
        padding: .4em;
        display: flex;
        justify-content: center;
        align-items: center;
        aspect-ratio : 1 / 1;
    }

    div.called div:nth-child(even) {
        background-color: darkgrey;
    }
    
    pre {
        font-size: 9pt !important;
    }

    td > div.called {
        font-size: 28pt;
        font-weight: 900;
        display: flex;
        padding: .4em;
        justify-content: center;
        align-items: center;
        aspect-ratio : 1 / 1;
        color: darkgreen;
        background-size: cover !important;
        background-color: lightgoldenrodyellow;
    }
    
    td > div.called.bingo {
	    animation: zoom 1s ease infinite;
    }

    div.ui.segment.inverted { 
        color: white;
        background: black;
        display: block;
        padding: 14px;
        border-top-left-radius: 4px;
        border-top-right-radius: 4px;
        border-bottom-left-radius: 0px;
        border-bottom-right-radius: 0px;
    }
    div.ui.segment.inverted.brown { 
        color: white;
        background-color: rgb(165, 103, 63);
        display: block;
        box-sizing: border-box;
        justify-content: space-around;
        padding: 14px;
        border-top-left-radius: 0px;
        border-top-right-radius: 0px;
        border-bottom-left-radius: 4px;
        border-bottom-right-radius: 4px;
    }

    ui.button {
        padding-top: 10px;
        padding-bottom: 10px;
        padding-right: 20px;
        padding-left: 20px;
        margin: 5px;
    }

    </style>
</head>
<body>
    <div class="ui gamebox">
        <!-- Game Controls -->
        <div class ="ui vertical segments">
            <div class="ui segment inverted"> Game Controls </div>
            <div class="ui segment inverted brown">
            <form action method="POST">
                <input type="hidden" class="ui button" name="action" value="new_game">
                <input type="submit"  value="Start New Game">
            </form>
            <form action method="POST">
                <input type="hidden" class="ui button" name="action" value="call_number">
                    <input type="submit"  value="Call New Number">
            </form>
            </div>
        </div>
        <!-- Bingo Card-->
        <div class="ui vertical segments">
            <div class="ui segment board">
                <table class="board">
                    <thead>
                        <tr>
                            <th>B</th>
                            <th>I</th>
                            <th>N</th>
                            <th>G</th>
                            <th>O</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Display the bingo card numbers from session data
                        if (isset($_SESSION['bingo_card'])) {
                            for ($row = 0; $row < 5; $row++) {
                                echo "<tr>";
                                // Iterate over columns
                                foreach ($_SESSION['bingo_card'] as $letter => $column) {
                                    echo "<td><div class='open'>" . $column[$row] . "</div></td>";
                                }
                                echo "</tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
                </div>
        </div>
        <!-- Called Numbers-->
        <div class="ui vertical segments">
            <div class="ui segment inverted">Numbers Called:</div>
            <div class="ui segment inverted brown called">
            <?php
                // Display the called numbers from session data
                if (isset($_SESSION['called_numbers'])) {
                    echo "<div>";
                    foreach ($_SESSION['called_numbers'] as $number) {
                        echo "<div>$number</div>";
                    }
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>