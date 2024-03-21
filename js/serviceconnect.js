function resolveFormatedTodisp(input) {
    parts2 = input.split("@");
    type = parts2[0];
    parts2.shift()
    value = parts2.join("@");
    if (type == "NULL") {
        return null;
    } else if (type == "BOOL") {
        if (value == "TRUE") {
            return true;
        } else {
            return false;
        }
    } else if (type == "ARRAY") {
        if (value.includes(";")) {
            pairs = value.split(";");
        } else {
            pairs = [value];
        }
        toReturn = {};
        pairs.forEach(pair => {
            parts3 = pair.split("=");
            key = parts3[0];
            value = parts3[1];
            toReturn[key] = value;
        });
        return toReturn;
    } else if (type == "STRING") {
        return value;
    } else if (type == "INT") {
        return parseInt(value);
    } else {
        return value;
    }
}

async function fetchPhpPage(url, params, parse=true) {
    try {
        // Convert parameters object to FormData
        const formData = new FormData();
        for (const key in params) {
            formData.append(key, params[key]);
        }

        // Fetch the PHP page using POST method
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });

        // Check if response is OK
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        // Get the raw content as a string
        const data = await response.text();
        if (parse == true) {
            return resolveFormatedTodisp(data);
        } else {
            return data;
        }
    } catch (error) {
        console.error('There was a problem with the fetch operation:', error);
        return null; // Return null if there's an error
    }
}

// Define functions:
async function getPostAccessee(serviceWorkerUrl,postID) {
    params = {
        "internal-call": true,
        "sendingmode": "get-accessees",
        "service_postid": postID
    }
    return await fetchPhpPage(serviceWorkerUrl, params);
}

async function addPostAccessee(serviceWorkerUrl,postID,clientID) {
    params = {
        "internal-call": true,
        "sendingmode": "add-accessee",
        "service_postid": postID,
        "service_clientid": clientID
    }
    return await fetchPhpPage(serviceWorkerUrl, params);
}

async function remPostAccessee(serviceWorkerUrl,postID,clientID) {
    params = {
        "internal-call": true,
        "sendingmode": "rem-accessee",
        "service_postid": postID,
        "service_clientid": clientID
    }
    return await fetchPhpPage(serviceWorkerUrl, params);
}

async function checkIfClientIsAccesseeByID(serviceWorkerUrl,postID,clientID) {
    params = {
        "internal-call": true,
        "sendingmode": "check-id-is-accessee",
        "service_postid": postID,
        "service_clientid": clientID
    }
    return await fetchPhpPage(serviceWorkerUrl, params);
}

async function getClientIDFromName(serviceWorkerUrl,username) {
    params = {
        "internal-call": true,
        "sendingmode": "client-id-from-username",
        "service_username": username,
    }
    return await fetchPhpPage(serviceWorkerUrl, params);
}

async function getClientIDFromDispName(serviceWorkerUrl,dispname) {
    params = {
        "internal-call": true,
        "sendingmode": "client-id-from-dispname",
        "service_dispname": dispname,
    }
    return await fetchPhpPage(serviceWorkerUrl, params);
}

async function getClientNameFromID(serviceWorkerUrl,clientID) {
    params = {
        "internal-call": true,
        "sendingmode": "client-username-from-id",
        "service_clientid": clientID,
    }
    return await fetchPhpPage(serviceWorkerUrl, params);
}

async function getClientNameFromID_ri(serviceWorkerUrl,clientID) {
    params = {
        "internal-call": true,
        "sendingmode": "client-username-from-id",
        "service_clientid": clientID,
    }
    output = await fetchPhpPage(serviceWorkerUrl, params)
    toRet = {}
    toRet["output"] = output;
    toRet["url"] = serviceWorkerUrl;
    toRet["clientID"] = clientID;
    return toRet;
}

async function addComment(serviceWorkerUrl,parentID,authorID,content,isForPost=null) {
    params = {
        "internal-call": true,
        "sendingmode": "client-username-from-id",
        "service_parentid": parentID,
        "service_authorid": authorID,
        "service_content": content
    }
    if (isForPost != null) {
        if (isForPost == false) {
            params["service_isforpost"] = "false";
        } else {
            params["service_isforpost"] = "true";
        }
    }
    return await fetchPhpPage(serviceWorkerUrl, params);
}

async function setComment(serviceWorkerUrl,commentID,content=null,authorID=null,parentID=null,isForPost=null) {
    params = {
        "internal-call": true,
        "sendingmode": "client-username-from-id",
        "service_commentid": commentID
    }
    if (content != null) {
        params["service_content"] = content;
    }
    if (authorID != null) {
        params["service_authorid"] = authorID;
    }
    if (parentID != null) {
        params["service_parentid"] = parentID;
    }
    if (isForPost != null) {
        if (isForPost == false) {
            params["service_isforpost"] = "false";
        } else {
            params["service_isforpost"] = "true";
        }
    }
    return await fetchPhpPage(serviceWorkerUrl, params);
}

async function remComment(serviceWorkerUrl,commentID) {
    params = {
        "internal-call": true,
        "sendingmode": "client-username-from-id",
        "service_commentid": commentID,
    }
    return await fetchPhpPage(serviceWorkerUrl, params);
}