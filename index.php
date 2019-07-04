<?php require_once('connection.php'); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">

    <title>Google maps</title>
	<style>
       #map {
        height: 400px;
        width: 100%;
       }
    </style>
  </head>
  <body>
    <h1>Google maps</h1>
    <form>
  <div class="form-group">
    <label for="exampleInputEmail1">Glorious Name</label>
    <input type="text" class="form-control" id="pointAddName" aria-describedby="emailHelp" placeholder="Enter GLORIOUS name">  
  </div>
    <div class="form-group">
    <label for="exampleInputEmail1">Glorious Latitude</label>
    <input type="text" class="form-control" id="pointAddLatitude" aria-describedby="emailHelp" placeholder="Enter GLORIOUS latitude">
  </div>
  <div class="form-group">
    <label for="exampleInputPassword1">Glorious Longitude</label>
    <input type="text" class="form-control" id="pointAddLongitude" placeholder="Enter GLORIOUS longitude">
  </div>
</form>
<button id="pointAddBtn" class="btn btn-primary">Add Marker</button>
<table class="table table-dark" id="table_list">
  <thead>
    <tr>
	    <th scope="col">ID</th>
	    <th scope="col">Name</th>
	    <th scope="col">Latitude</th>
	    <th scope="col">Longitude</th>
	    <th scope="col">Update</th>
	    <th scope="col">Delete</th>
    </tr>
  </thead>
  <tbody id="tableBody">
  </tbody>
</table>
	<div id="map"></div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
	
	<script>
      function initMap() {
      	var markers = [];
        var uluru = {lat: -25.363, lng: 131.044};
        var map = new google.maps.Map(document.getElementById('map'), {
			zoom: 4,
			center: uluru
        });
        var marker = new google.maps.Marker({
		    position: uluru,
		    map: map
        });

        $.getJSON("get_markers.php", function(data){
        	if(data.status == 1){
	            for(i = 0; i< data.markers.length; i++){
		            const curMarker = data.markers[i];
	          		addNewMarker(curMarker);
	            	$('#table_list').append( getTableRow(curMarker.ID, curMarker.name, curMarker.latitude, curMarker.longitude));
	           	}
        	}else{
            	$(".jquery.message").html('<div class="alert alert-' + alertClass + '">I have no fucking clue what I\'m doing </div>');
          	}
        });

        $("body").on("click", "#updateMarker", function(){
	       	const buttonID = $(this).val(),
	       		name = $("#pointAddName").val(),
	            lat = $("#pointAddLatitude").val(),
	            lng = $("#pointAddLongitude").val();
                updateTableRow(name, lat, lng, buttonID);
        });

        $("body").on("click", "#deleteMarker", function(){
        	const buttonID = $(this).val();
        	console.log(buttonID);
        	$.post("jquery/deletePoint.php", {ID: buttonID}, function (data){
        		console.log(data);
        		const jsonData = JSON.parse(data);
        		if(jsonData.status == 1){
        			$('#table_list tr').each(function(){
        				var tableID = $(this).find("td:first").html();
        				if(tableID == jsonData.ID){
        					$(this).remove();
        					deleteMarker(jsonData.ID);
        				}
        			});
        		}
        	});
        });

        google.maps.event.addListener(map, 'click', function(event) {
	        $("#pointAddLatitude").val(event.latLng.lat);
	        $("#pointAddLongitude").val(event.latLng.lng);
        });

        $("#pointAddBtn").on("click", function(){
        	const name = $("#pointAddName").val(),
	            lat = $("#pointAddLatitude").val(),
	            lng = $("#pointAddLongitude").val();
        	$.post("jquery/insertNewPoint.php", {name: name, lat: lat, lng: lng})
        	.done(function(data){

                const json = JSON.parse(data);
                console.log(json.ID);

                if(json.ID != ""){
                    addNewMarker(getMarkerObject(json.ID, name, lat, lng));
                    $('#table_list').append( getTableRow(json.ID, name, lat, lng));
                }
	            
	            
                let alertClass = 'info';
	            if(json.status == 0){
	              alertClass = 'warning';
	            }
	            $(".jquery.message").html('<div class="alert alert-' + alertClass + '">I have no fucking clue what I\'m doing </div>');
        	});
        });

        function updateTableRow(name, lat, lng, ID){
        	console.log("name: " + name + " lat: " + lat + " lng: " + lng + " ID: " + ID);

        	$.post("jquery/updateOldPoint.php", {name: name, lat: lat, lng: lng, ID: ID}, function(data){
	          	const jsonData = JSON.parse(data);
                console.log(jsonData);
	          	if(jsonData.status == 1){
		          	$('#table_list tr').each(function() {
		    			var tableID = $(this).find("td:first").html();    
		    			if(tableID == ID){
		    				var newTableRow = getTableRow(tableID, name, lat, lng);
		    				updateMarker(getMarkerObject(tableID, name, lat, lng));
		    				console.log(newTableRow);
		    				$(this).replaceWith(newTableRow);
		    			}
					});
	          	}
        	});
        }
        
        function deleteMarker(ID){
        	if(markers[ID] != 'undefined'){
        		markers[ID].setMap(null);
        	}
        }

        function addNewMarker(marker){
        	const latitude = marker.latitude,
        		longitude = marker.longitude;
	        markers[marker.ID] = new google.maps.Marker({
                position: {lat: parseFloat(latitude), lng: parseFloat(longitude)},
                draggable: true,
                map: map
        	});
       	 	markers[marker.ID].id = marker.ID;
       	 	markers[marker.ID].name = marker.name;
        	markers[marker.ID].addListener('dragend', dragEnded);
        }

        function dragEnded(event){
        	const positions = event.latLng;
        	updateTableRow(this.name, positions.lat(), positions.lng(), this.id);
        }

        function updateMarker(marker){
        	deleteMarker(marker.ID);
        	addNewMarker(marker);
        }

        function getMarkerObject(ID, name, latitude, longitude){
        	return {ID: ID, name: name, latitude: latitude, longitude: longitude};
        }

        function getTableRow(ID, name, lat, long){
			return	"<tr>" +
		              	"<td>" + ID + "</td>" +
		                "<td>" + name + "</td>" +
		                "<td>" + lat + "</td>" +
		                "<td>" + long + "</td>" +
		                "<td><button id='updateMarker' value='" + ID + "' class='btn btn-primary'>Update</button></td>" +
		                "<td><button id='deleteMarker' value='" + ID + "' class='btn btn-danger'>Delete</button></td>" +
	                "</tr>";
        }
      }
    </script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD2lY-gUf_WQjXsClERmoVzqH166AzueiY&callback=initMap"></script>
  </body>
</html>