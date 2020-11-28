<!--
 * @author Carlos Pinto
 * @email kkobtk@gmail.com
 * @create date 2020-11-28 12:50:22
 * @modify date 2020-11-28 12:50:22
 * @desc Mastermind game for technical test
 -->

 <!DOCTYPE html>
<html lang="en-US">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="./style.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

  <title>Mastermind</title>
</head>

<body>
  <h1>Mastermind</h1>
  <br>

<?php
//Global Variables
$max_attempts = 10;
$colors = array(
    "red",
    "green",
    "blue",
    "yellow"
);

//Modal with the game rules
require "rules-modal.html";

//start or continue the session
session_start();

/* ---------- FUNCTIONS ---------- */

/**
 * Prints the right colors when the user lose, can be used for debugging.
 */
function printRightColors()
{
    echo "
    <br>
      <table class='table' id='correct-colors'>
        <tr>
          <th>Right colors:</th>
          <td> <p class='circle " . $_SESSION["values"][0] . " '></p> </td>
          <td> <p class='circle " . $_SESSION["values"][1] . " '></p> </td>
          <td> <p class='circle " . $_SESSION["values"][2] . " '></p> </td>
          <td> <p class='circle " . $_SESSION["values"][3] . " '></p> </td>
        </tr>
      </table>
      ";
}

/**
 * Prints a 2-dimensional array attempts table, stored in $_Session["attempts"]
 * with the following structure:
 *      N.attempt, colors, suggestions
 *
 * Also prints how many colors are in the right or wrong.
 * $_SESSION["attempts"][4] =  black peg
 * $_SESSION["attempts"][5] =  white peg
 */
function printAttempts()
{
    //print the table headers
    echo "<hr>
    <h5>Attempts made</h5>
    <table class='table' id='attempts'>
      <tr>
      <th scope='col col-md-1'>N°</th>
      <th scope='col col-md-1'>1</th>
      <th scope='col col-md-1'>2</th>
      <th scope='col col-md-1'>3</th>
      <th scope='col col-md-1'>4</th>
      <th scope='col col-md-4'>Results</th>
      </tr>";

    if (isset($_SESSION["attempts"]))
    { //check if there are any saved attempts
        echo "<tr>";

        foreach ($_SESSION["attempts"] as $attemptNumber => $row)
        {

            echo "<th>" . ($attemptNumber + 1) . "</th>"; //prints the attempt number
            foreach ($row as $key => $value)
            { //iterate each row
                if ($key == 4)
                {
                    //$_SESSION["attempts"][4] =  black peg
                    echo "<td> ";

                    for ($i = 0;$i < $value;$i++)
                    {
                        echo "<p class='circle black'></p>";
                    }
                }
                elseif ($key == 5)
                { //$_SESSION["attempts"][5] =  white peg
                    for ($i = 0;$i < $value;$i++)
                    {
                        echo "<p class='circle white'></p>";
                    }
                    echo "</td>";
                }
                else echo "<td> <p class='circle  $value '></p> </td>"; //Prints the peg
                
            }
            echo "</tr>";
        }
    }

    echo "</table>";
}

/** Function to check how many values of $attempt are in $rightCombination, then stores them in $_SESSION["attempts"][LAST_ATTEMPT][5]
 * The $attempt array should contain only values that aren't in the right position = white pegs
 * This function must always be called only by rightPosition()
 * @param $attempt user's attempt
 * @param $rightCombination the right color combination.
 */
function rightColor($attempt, $rightCombination)
{
    $rightColorNumbers = 0;

    foreach ($attempt as $value)
    {
        if (array_search($value, $rightCombination) !== false)
        { //!==FALSE must be used because array_search may return 0
            $rightColorNumbers++;

            unset($rightCombination[array_search($value, $rightCombination) ]); //removes the value from $rightCombination, to prevent possible problems when in $attempt with duplicate colors
            
        }
    }
    $_SESSION["attempts"][count($_SESSION["attempts"]) - 1][5] = $rightColorNumbers; //Store final value
    
}

/** Function that checks how many colors of the attempt are black pegs, then stores it in $_SESSION["attempts"][LAST_ATTEMPT][4]
 * This function then calls the rightColor() method, to check for the number of white pegs
 */
function rightPosition()
{
    $attempt = array(
        $_POST["0"],
        $_POST["1"],
        $_POST["2"],
        $_POST["3"]
    );
    $rightCombination = $_SESSION["values"];
    $rightPositionNumber = 0;

    foreach ($rightCombination as $key => $val)
    {
        if ($val == $attempt[$key])
        {
            $rightPositionNumber++;

            //if there's a match, it removes the values from both arrays
            unset($attempt[$key]);
            unset($rightCombination[$key]);
        }
    }

    //Storee the result in $_SESSION["attempts"][LAST_ATTEMPT][4]
    $_SESSION["attempts"][count($_SESSION["attempts"]) - 1][4] = $rightPositionNumber;

    //Checking the number of white pegs
    rightColor($attempt, $rightCombination);
}

/* ---------- REQUEST LOGIC ---------- */

if (isset($_SESSION["values"]))
{ //Checks if there's a match stored at any position
    if (isset($_POST['0']) && isset($_POST['1']) && isset($_POST['2']) && isset($_POST['3']))
    {

        //Save attempt
        if (isset($_SESSION["attempts"]))
        {

            //Pushing another attempt to $_SESSION["attempts"] array
            array_push($_SESSION["attempts"], array(
                $_POST["0"],
                $_POST["1"],
                $_POST["2"],
                $_POST["3"]
            ));

            rightPosition(); //Check white and black pegs
            
        }
        else
        {
            //Creating the attempt array ($_SESSION["attempts"]
            $_SESSION["attempts"] = array(
                array(
                    $_POST["0"],
                    $_POST["1"],
                    $_POST["2"],
                    $_POST["3"]
                )
            );

            rightPosition(); //Check for white and black pegs
            
        }

        if ( //If solved
        $_POST["0"] == $_SESSION["values"][0] && $_POST["1"] == $_SESSION["values"][1] && $_POST["2"] == $_SESSION["values"][2] && $_POST["3"] == $_SESSION["values"][3])
        {
            echo "<h1 class='label' id='win'>Congrats, you cracked the code!</h1>
          <form action=\"index.php\" method=\"GET\">
              <button class=\"btn btn-outline-primary\" type=\"submit\">Play again</button>
          </form>";

            session_destroy();
        }
        else if (count($_SESSION["attempts"]) >= $max_attempts)
        { //If not solved
            echo "<h1 class='label' id='lost'>Game over</h1>
          <form action=\"index.php\" method=\"GET\">
              <button class=\"btn btn-outline-primary\" type=\"submit\">Play again</button>
          </form>";

            printRightColors(); //warn the player of the right colors
            session_destroy();
        }
        else require "input-form.html";
        
    }
    else require "input-form.html"; 
    
}
else
{ //Randomly generating the values to be guessed (color combination)
    $_SESSION["values"] = array(
        $colors[rand(0, 3) ],
        $colors[rand(0, 3) ],
        $colors[rand(0, 3) ],
        $colors[rand(0, 3) ]
    );
    require "input-form.html";
}


//printRightColors(); // Uncomment this only for debugging.
echo "<br><br>";


printAttempts();

?>
  <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>

</html>
