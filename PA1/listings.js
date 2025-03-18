//Why I Chose Asynch

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//LISTINGS

var loadingscreen=document.getElementById("loading_screen");


function getAllListings(selectedOption, selectedSort, searchValue) {

    loadingscreen.style.display="flex";
    var xhr = new XMLHttpRequest();
    var url = "https://wheatley.cs.up.ac.za/api/";

    var requestData = {
        "studentnum": "u22566202", 
        "apikey": "90b360c7b3ccee2b50bdbf8b024f97bc", 
        "type": "GetAllListings",
        "limit": 30, 
        "return": ["id", "title", "location", "price", "bedrooms", "bathrooms", "parking_spaces", "type"],
        "sort": "price",
        "order": "ASC"
    };

    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-Type", "application/json");

    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 300) {
            var response = JSON.parse(xhr.responseText);
            if (response.status === "success") {
                
                var listings = response.data;
              
                // console.log("Listings: ", listings);

                // Filter the listings
                if (searchValue) {
                    listings = listings.filter(function(listing) {
                        return listing.title.toLowerCase().includes(searchValue.toLowerCase());
                    });
                } 

                // Sort and filter the listings
                if (selectedSort === "price") {
                    listings.sort(function(a, b) {
                        return a.price - b.price;
                    });
                } else if (selectedSort === "title") {
                    listings.sort(function(a, b) {
                        return a.title.localeCompare(b.title);
                    });
                } else if (selectedOption === "rent") {
                    listings = listings.filter(function(listing) {
                        return listing.type === "rent";
                    });
                } else if (selectedOption === "buy") {
                    listings = listings.filter(function(listing) {
                        return listing.type === "buy";
                    });
                }


                displayListings(listings);
            } else {
                console.error("Could not retrieve listings:", response.message);
            }
        } else {
            console.error("Could not retrieve listings:", xhr.status);
        }

        loadingscreen.style.display="none";
    };

    xhr.send(JSON.stringify(requestData));

   
}

function openImageForListing(listingId) {
    // var container = document.getElementById('image-container');
     
     var img_url = "https://wheatley.cs.up.ac.za/api/getimage?listing_id=" + listingId;
    
     return new Promise(function(resolve,reject){
     var xxtp = new XMLHttpRequest();
     xxtp.open("GET", img_url, true);
 
     xxtp.onload = function() {
         if (xxtp.status >= 200 && xxtp.status < 300) {
             var responseData = JSON.parse(xxtp.responseText);
             if (Array.isArray(responseData.data) && responseData.data.length > 0) {
                 // Take only the first image
                 var imagePath = responseData.data[0];
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

 
async function displayListings(listings) {
    var container = document.getElementById('listings-container');
    container.innerHTML =""; 

    var image_array=[];

    //array for loading and storing imagespaths
    for(var x=0;x<30;x++)
    {
        //opens image
        var listing=listings[x];
    

        try
        {
            var path = await openImageForListing(listing.id);
            image_array.push(path);
        }
        catch(error)
        {
            console.log(error);
        }

    }

    for (var i = 0; i < listings.length; i++) {
        var listing = listings[i];

        var listingDiv = document.createElement('div');


        listingDiv.classList.add('listing');


          
        var my_image=document.createElement("img");
        my_image.src=image_array[i];


        
         listingDiv.appendChild(my_image);
        
       // listingDiv.appendChild(linkElement);


        var title = document.createElement('h2');
        title.textContent = listing.title;
        listingDiv.appendChild(title);

        var informatiom=document.createElement("p");
        informatiom.textContent= "Location: " + listing.location+" | Price:R " + listing.price+" | Bedrooms: "+listing.bedrooms+" | Bathrooms: "+listing.bathrooms;
        listingDiv.appendChild(informatiom);
        container.appendChild(listingDiv);
        // Open image for the current listing
       

        container.appendChild(listingDiv);
            
         //Creating a button for more information
        var linkElement = document.createElement("a");
        linkElement.href = "view.html?id="+listing.id; 
       // linkElement.target = '_blank';
        
        const moreinfo = document.createElement("button");
        moreinfo.textContent = "Click For More";
        linkElement.appendChild(moreinfo); // Append the button to the link
        
       

        listingDiv.appendChild(linkElement);
        

       // container.appendChild(listingDiv);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var option = document.querySelector('.buy-rent-selector');
    var sort = document.getElementById('sort');
    var search = document.getElementById('searchbar');

    //get listings
    getAllListings(selectedOption, selectedSort);

    // DEFAULT VALUES
    var selectedOption = option.value; // Default value is "buy"
    var selectedSort = sort.value; //Default value is "title"

    // Add event listener event changes
    option.addEventListener('change', function() {
        var selectedOption = option.value;
        getAllListings(selectedOption, selectedSort);
    });

    // Add event listener event changes
    sort.addEventListener('change', function() {
        var selectedSort = sort.value;
        getAllListings(selectedOption, selectedSort);
    });

    // Add event listener event changes
    search.addEventListener('input', function() {
        var searchValue = search.value;
        getAllListings(selectedOption, selectedSort, searchValue);
    });
});
