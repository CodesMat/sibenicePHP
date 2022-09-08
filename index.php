<?php
include_once 'conn.php';
session_start();

$pismenos = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
$vyhra = false;


//obrazky
$castiTela = ["obr.1","obr.2","obr.3","obr.4","obr.5","obr.7"];


  $sql = "SELECT * FROM slova  ORDER BY RAND () LIMIT 1 ; ";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
$slovo = [ $row['slovo']];
/*print_r($slovo);  */
    }
}

/*$slovo = ["KAREL"];*/





function getCurrObrazek($cast){
    return "./obr/". $cast. ".png";
}


function zacitHru(){
   session_start();
}

// restart SESNY
function restartovatHru(){
    session_destroy();
    session_start();}
function getCasty(){

    global $castiTela;
    return isset($_SESSION['castiH']) ? $_SESSION['castiH'] : $castiTela;
}

// pridani casti hanhmana
function pridatTelo(){
    $castiH = getCasty();
    array_shift($castiH);
    $_SESSION['castiH'] = $castiH;
}

// get na cast tela hangmana
function getCurrentCast(){
    $castiH = getCasty();
    return $castiH[0];
}


function getSlovo(){
    global $slovo;
    if(!isset($_SESSION["slovo"]) && empty($_SESSION["slovo"])){
        $key = array_rand($slovo);
        $_SESSION["slovo"] = $slovo[$key];
    }
    return $_SESSION['slovo'];
}




// get odpoved
function getOdpoved(){
    return isset($_SESSION["responses"]) ? $_SESSION["responses"] : [];
}

function addOdpoved($pismeno){
    $responses = getOdpoved();
    array_push($responses, $pismeno);
    $_SESSION["responses"] = $responses;
}

// check if pressed letter is correct
function jePismenoSpravne($pismeno){
    $slovo = getSlovo();
    $max = strlen($slovo) - 1;
    for($i=0; $i<= $max; $i++){
        if($pismeno == $slovo[$i]){
            return true;
        }
    }
    return false;
}


function isWordCorrect(){
    $guess = getSlovo();
    $responses = getOdpoved();
    $max = strlen($guess) - 1;
    for($i=0; $i<= $max; $i++){
        if(!in_array($guess[$i],  $responses)){
            return false;
        }
    }
    return true;
}



function kompletniTeloCheck(){
    $castiH = getCasty();
    
    if(count($castiH) <= 1){
        return true;
    }
    return false;
}


function konecHry(){
    return isset($_SESSION["gamecomplete"]) ? $_SESSION["gamecomplete"] :false;
}


// set konec hry
function setKonecHry(){
    $_SESSION["gamecomplete"] = true;
}

// set nova hra
function setNovaHra(){
    $_SESSION["gamecomplete"] = false;
}



/* když je nastavena submit tak resetovat hru*/
if(isset($_GET['start'])){
    header("Location: ./index.php");
    restartovatHru();
}


/* key press */
if(isset($_GET['pismeno'])){
    $currentPressedKey = isset($_GET['pismeno']) ? $_GET['pismeno'] : null;
    // key pressed
    if($currentPressedKey 
    && jePismenoSpravne($currentPressedKey)
    && !kompletniTeloCheck()
    && !konecHry()){
        
        addOdpoved($currentPressedKey);
        if(isWordCorrect()){
            $vyhra = true;
            setKonecHry();
        }
    }else{
        // veseni 
        if(!kompletniTeloCheck()){
           pridatTelo(); 
           if(kompletniTeloCheck()){
               setKonecHry(); 
           }
        }else{
            setKonecHry();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <title></title>
</head>
    <body>

        <div class="container" >
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
 
  
  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" href="pridavaniSlov.php">přidat slovo</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Hrát</a>
      </li>
</nav>
            <div class="row">
                <div class="col-md-12">
            
           
            <div class="col-md-6">
                 
          
                <!-- konec hry -->
               <?Php if(konecHry()):?>
                    <h1>KONEC HRY</h1>
                <?php endif;?>
                <?php if($vyhra  && konecHry()):?>
                    <h1 style="color:green;">vyhrál si <form method="GET"><button class="btn btn-dark" style="margin:2%;" type="submit" name="start">Hrát Znovu</button></form> </h1>
                <?php elseif(!$vyhra  && konecHry()): ?>
                    <h1 style="color:red;" >Prohrál si <form method="GET"><button class="btn btn-dark" style="margin:2%;" type="submit" name="start">Hrát Znovu</button></form></h1>
                <?php endif;?>
                <img height="150" width="150"  src="<?php echo getCurrObrazek(getCurrentCast());?>"/>
            </div>
            
            <div class="col-md-6"  >
                <h1>šibenice</h1>
                <div  >
                    <form method="GET">
                    <?php
                    /*vykresleni vsech pismen*/
                        $max = strlen($pismenos) - 1;
                        for($i=0; $i<= $max; $i++){
                           ?> <button  type='submit' class="btn btn-light" name='pismeno' value= "<?php echo $pismenos[$i]?>">
                           <?php echo $pismenos[$i] ?>  </button><?php
                            if ($i % 7 == 0 && $i>0) {
                               echo '<br>';
                            }
                            
                        }
                    ?>
                   <br>
                    
                    
                    </form>
                </div>
            </div>
            
            <div class="col-md-12" style="" >
                <!-- zobraz hadan slova -->
                <?php 
                 $guess = getSlovo();
                 $maxLetters = strlen($guess) - 1;
                 
                for($j=0; $j<= $maxLetters; $j++): $l = getSlovo()[$j]; ?>
                    <?php  if(in_array($l, getOdpoved())):?>
                        <span style="border-bottom: 1px solid black;" ><?php echo $l;?></span>
                    <?php else: ?>
                      
                    <?php endif;?>
                <?php endfor;?>
            </div>  
        </div>
        </div>
        </div>
</body>
</html>