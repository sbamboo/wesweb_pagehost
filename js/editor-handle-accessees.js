

// get url
parts = window.location.href.split("/");
parts.pop();
parts.push("php/_serviceworker.php");
const servWorkURL = parts.join("/");

function populateAccessees(postID) {
    // get element
    parent2 = document.getElementById("js-fillable_post-accessees");
    parent2.innerHTML = "";

    // Get accessees
    getPostAccessee(servWorkURL,postID)
        .then(accessees => {
            // Display accessees
            for (const key in accessees) {
                accesseeID = accessees[key];
                getClientNameFromID_ri(servWorkURL,accesseeID)
                    .then(output => {
                        accesseeName2 = output["output"];
                        accesseeID2 = output["clientID"];
                        parent2.innerHTML += `
                        <div class="editor-accessee-list-item flex-horiz">
                            <p class="editor-accessee-list-item-name">${accesseeName2} (ID: ${accesseeID2})</p>
                            <button class="editor-accessee-list-item-removebtn">Remove</button>
                        </div>
                        `;
                        remButton = parent2.getElementsByClassName("editor-accessee-list-item-removebtn")[0];
                        remButton.onclick = () => {
                            parent2.innerHTML = '<p id="accessee-list-still-working-msg">Working...</p>';
                            remPostAccessee(servWorkURL,postID,accesseeID2)
                                .then( state => {
                                    //populateAccessees(postID);
                                    setTimeout(() => {populateAccessees(postID);}, 1000);
                                });
                        };
                    })
            }
        });
}

function addAccessee(postID,input,text) {
    
    parent2 = document.getElementById("js-fillable_post-accessees");
    parent2.innerHTML = '<p id="accessee-list-still-working-msg">Working...</p>';

    // Clear msg
    text.innerText = "";

    // Get id/name
    content = input.value;

    // If not id get id from name
    if (content.includes("id:")) {
        id = parseInt(content.replace("id:","",1));
        addPostAccessee(servWorkURL,postID,id)
            .then( state => {
                //populateAccessees(postID);
                setTimeout(() => {populateAccessees(postID);}, 1000);
            });
    } else {
        getClientIDFromDispName(servWorkURL,content)
            .then(id => {
                console.log(id);
                addPostAccessee(servWorkURL,postID,id)
                    .then( state => {
                        //populateAccessees(postID);
                        setTimeout(() => {populateAccessees(postID);}, 1000);
                    });
            });
    }

}

function populateAccesseeActionButtons(postID) {
    // get element
    parent = document.getElementById("js-fillable_accessee-actionbuttons");
    parent.innerHTML = "";

    // Create add interface
    const input = document.createElement("input");
    input.id = "js-handled_accesse-add-action-input";
    const text = document.createElement("p");
    text.id = "js-handled_accesse-add-action-text";
    const button = document.createElement("button");
    button.id = "js-handled_accesse-add-action-button";
    button.innerText = "Add accessee";
    button.onclick = () => {
        addAccessee(postID,input,text);
    };
    parent.appendChild(input);
    parent.appendChild(text);
    parent.appendChild(button);
}
