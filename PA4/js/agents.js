
var loadingscreen=document.getElementById("loading_screen");


function getAllAgents() {
    loadingscreen.style.display="flex";
    var xhr = new XMLHttpRequest();
    var url = 'https://wheatley.cs.up.ac.za/api/';

    var requestData = {
        "studentnum": "u22566202", // Replace with your student number
        "apikey": "90b360c7b3ccee2b50bdbf8b024f97bc", // Replace with your API key
        "type": "GetAllAgents",
        "limit": 15, // Specify the number of agents you want to retrieve
    };

    xhr.open('POST', url, true);
    xhr.setRequestHeader('Content-Type', 'application/json');

    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 300) {
            var response = JSON.parse(xhr.responseText);
            if (response.status === "success") {
               
                var agents = response.data;
               
                console.log(agents);
                displayAgents(agents);
            } else {
                console.error('Error retrieving agents:', response.message);
            }
        } else {
            console.error('Error retrieving agents:', xhr.status);
        }

        loadingscreen.style.display="none";
    };

    xhr.send(JSON.stringify(requestData));
}

function displayAgents(agents) {
    var container = document.getElementById('agents-container');
    container.innerHTML = ''; 

    for (var i = 0; i < agents.length; i++) {
        var agent = agents[i];

        var agentDiv = document.createElement('div');
        agentDiv.classList.add('agent');

        var name = document.createElement('h2');
        name.textContent = agent.name;
        agentDiv.appendChild(name);

        var description = document.createElement('p');
        description.textContent = agent.description;
        agentDiv.appendChild(description);

        var url = document.createElement('a');
        url.textContent = 'Visit Website';
        url.href = agent.url;
        url.target = '_blank'; 
        agentDiv.appendChild(url);

       
        container.appendChild(agentDiv);

        
        openImageForAgent(agent.name, agentDiv);
    }
}

function openImageForAgent(agency, agentDiv) {
    var img_url = "https://wheatley.cs.up.ac.za/api/getimage?agency=" + encodeURIComponent(agency);

    var xhr = new XMLHttpRequest();
    xhr.open("GET", img_url, true);

    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 300) {
            var responseData = JSON.parse(xhr.responseText);
            if (responseData.status === "success") {
                var imagePath = responseData.data;
                if (typeof imagePath === 'string') {
                    // Directly use the string value as the image path
                    var img = document.createElement("img");
                    img.src = imagePath;
                    img.alt = `Agency ${agency} Image`;
                    img.width="600";
                    img.height="600";


                    // Append the image to the agent div
                    agentDiv.appendChild(img);
                } else if (Array.isArray(imagePath) && imagePath.length > 0) {
                    // Take only the first image if it's an array
                    var img = document.createElement("img");
                    img.src = imagePath[0];
                    img.alt = `Agency ${agency} Image`;

                    // Append the image to the agent div
                    agentDiv.appendChild(img);
                } else {
                    console.error("No image found for agency:", agency);
                }
            } else {
                console.error("Request failed:", responseData.message);
            }
        } else {
            console.error("Request failed:", xhr.status);
        }
    };

    xhr.send();
}


// Call the function to retrieve agents
getAllAgents();

