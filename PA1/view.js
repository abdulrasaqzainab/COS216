var loadingscreen=document.getElementById("loading_screen");

function getListingIdFromUrl() {
    var urlParams = new URLSearchParams(window.location.search);
    console.log("URL:", window.location.href);
    var listingId = urlParams.get('id');
    return listingId;
}


function getbuttonListings() {
    var listingId = getListingIdFromUrl();
    if (!listingId) {
        console.error('Listing ID not found in URL');
        return;
    }
    loadingscreen.style.display="flex";
    var xhr = new XMLHttpRequest();
    var url = 'https://wheatley.cs.up.ac.za/api/';


    var requestData = {
        "studentnum": "u22566202", 
        "apikey": "90b360c7b3ccee2b50bdbf8b024f97bc", 
        "type": "GetListingById",
        "search" : {
          "id": listingId
        },
         "return": ["id", "title", "location", "price", "bedrooms", "bathrooms", "parking_spaces", "type"]
        
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

        loadingscreen.style.display="none";
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
 


function displayListing(listing,listingId)
{
    var container = document.getElementById('view-container');
    container.innerHTML = "";

   openImageForListingsbutton(listingId)
   .then(function(imagePath)
{
    for(var x=0;x<imagePath.length;x++)
    {
        var img=document.createElement("img");
        img.src=imagePath[x];
        container.appendChild(img);
        //add carousel after this
    }
})
.catch(function(error)
{
    console.error("Could not retrieve the image",error);
})

//append extra info i want to display
}



//displaying from button clicked

getbuttonListings();

