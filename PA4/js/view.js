//var loadingscreen=document.getElementById("loading_screen");

function getListingIdFromUrl() {
    var urlParams = new URLSearchParams(window.location.search);
    console.log("URL:", window.location.href);
    var listingId = urlParams.get('id');
    return listingId;
}
var listingId = getListingIdFromUrl();

function getbuttonListings() {
    var listingId = getListingIdFromUrl();
    if (!listingId) {
        console.error('Listing ID not found in URL');
        return;
    }
    //loadingscreen.style.display="flex";
    var xhr = new XMLHttpRequest();
    var url = 'https://wheatley.cs.up.ac.za/api/';


    var requestData = {
        "studentnum": "u22566202", 
        "apikey": "90b360c7b3ccee2b50bdbf8b024f97bc", 
        "type": "GetAllListings",
        "limit": 30, 
        "search" : {
            "id": listingId
          },
        "return": ["id", "title", "location", "price", "bedrooms", "bathrooms", "parking_spaces", "type","amenities","description"],
        "sort": "price",
        "order": "ASC"
    };
    xhr.open('POST', url, true);
    xhr.setRequestHeader('Content-Type', 'application/json');

    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 300) {
            var response = JSON.parse(xhr.responseText);
            if (response.status === "success") {
                var listings = response.data;
               
                console.log(listings);
                displayListing(listings,listingId);
            } else {
                console.error("Could not retrieve listing:", response.message);
            }
        } else {
            console.error("Could not retrieve listing:", xhr.status);
        }

     //   loadingscreen.style.display="none";
    };

    xhr.send(JSON.stringify(requestData));

   
}


function openImageForListingsbutton(listingId) {
    // var container = document.getElementById('image-container');
     
     var img_url = "https://wheatley.cs.up.ac.za/api/getimage?listing_id=" + listingId;
    
     return new Promise(function(resolve,reject){
     var xxtp = new XMLHttpRequest();
     xxtp.open("GET", img_url, true);
 
     xxtp.onload = function() {
         if (xxtp.status >= 200 && xxtp.status < 300) {
             var responseData = JSON.parse(xxtp.responseText);
             if (Array.isArray(responseData.data) && responseData.data.length > 0) {
                 // Take all images
                 var imagePath = responseData.data;
                resolve(imagePath);
             } else {
                 console.error("No image found for listing:", listingId);
                 
             }
         } else {
             reject("Request failed :", xxtp.status);
             
         }
     };
 
     xxtp.send();
    });
 }
 


 function displayListing(listing, listingId) {
    var container = document.getElementById('view-container');
    container.innerHTML = "";
    

    var listingDiv = document.createElement('div');
            listingDiv.classList.add('listing');

            var title = document.createElement('h2');
            title.textContent = listing.title;
            listingDiv.appendChild(title);

            var information = document.createElement("p");
            information.textContent = "Location: " + listing.location + " | Price: R " + listing.price + " | Bedrooms: " + listing.bedrooms + " | Bathrooms: " + listing.bathrooms;
            listingDiv.appendChild(information);

            var desc=document.createElement("h2");
            desc.textContent="Description:";
            listingDiv.appendChild(desc);
            desc=document.createElement("p");
            desc.textContent=listing.description;
            listingDiv.appendChild(desc);

            var additional=document.createElement("p");
            additional.textContent="Parking Space: "+listing.parking_spaces+"|"+"Amenities: "+listing.amenities;
            listingDiv.appendChild(additional);

            container.appendChild(listingDiv);
    openImageForListingsbutton(listingId)
        .then(function(imagePath) {
           
            var imagcontainer=document.createElement("div");
            for (var x = 0; x < imagePath.length; x++) {
                var img = document.createElement("img");
                img.src = imagePath[x];
                imagcontainer.appendChild(img);
                // Add carousel logic here
            }

        
            container.appendChild(imagcontainer);

            
        })
        .catch(function(error) {
            console.error("Could not retrieve the image", error);
        });

    // Append extra info here if needed
}



//displaying from button clicked

getbuttonListings();

