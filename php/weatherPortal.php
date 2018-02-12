<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
div {
    border: 1px solid black;
}
body {
	font-family: Arial;
  	margin:0;
  	padding:0;
    div {
    border: 5px solid red;
}
    }
    
.container {
    display: grid;
    grid-template-columns: repeat(auto-fill, 100px);
    grid-template-rows: auto;
}

/* Style the tab */
.tab {
	position: fixed;
    width:100%;
    overflow: hidden;
    top: 0;
    left:0;
  	z-index: 10;
    background-color: #f1f1f1;
}

/* Style the buttons inside the tab */
.tab button {
    background-color: inherit;
    float: left;
    border: none;
    outline: none;
    cursor: pointer;
    padding: 14px 16px;
    transition: 0.3s;
    font-size: 17px;
}

/* Change background color of buttons on hover */
.tab button:hover {
    background-color: #ddd;
}

/* Create an active/current tablink class */
.tab button.active {
    background-color: #ccc;
}

/* Style the tab content */
.tabcontent {
	width:100%;
	z-index:0;
    padding: 50px 16px;
    top:10;
    left:0;
    display: none;
    border-bottom: 1px solid #ccc;
}
</style>


<div class="tab">
  <button class="tablinks" onclick="openCity(event, 'Today')" id="defaultOpen">Today</button>
  <button class="tablinks" onclick="openCity(event, 'Annual')">Annual</button>
</div>

<div id="Today" class="tabcontent">
  <h3>Today</h3>
  <p>Today's weather</p>
</div>

<div id="Annual" class="tabcontent">
  <h3>Annual</h3>
  <p>Annual weather</p> 
</div>


</head>
<body>







<div class="container">
  <div>1</div>
  <div>2</div>
  <div>3</div>
  <div>4</div>
  <div>5</div>
  <div>6</div>
</div>
    
    
    

<script>
function openCity(evt, cityName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(cityName).style.display = "block";
    evt.currentTarget.className += " active";
}

// Get the element with id="defaultOpen" and click on it
document.getElementById("defaultOpen").click();
</script>
     
</body>
</html> 
