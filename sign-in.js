const submitBtn = document.querySelector("#submit-btn");
const formInputs = document.querySelectorAll(".form-control");
const formError = document.getElementById("form-error");

async function registerUser (payload, credentials) {
    const response = await $.ajax("http://localhost/test-api/api/index.php/register",
        {data: payload, method: "POST", headers: {Authorization:"Basic " + credentials}});

    return response;
}

function submitSignInHandler (event) {
    event.preventDefault();
    const data = {};

    for (const item of formInputs) {
        data[item.id] = item.value;
    }
    data["isadmin"] = 1;

    let credentials = data["username"] + ":" + data["password"];
    delete data["username"];
    delete data["password"];

    credentials = btoa(credentials);

    registerUser(data, credentials).then(data => {
        console.log(data)
        formError.innerText = "";
        window.location.href = "http://localhost/test-front/log-in.html";
    }).catch(err => {
        console.log(err.status);
        if (err.status === 409) {
            formError.innerText = "Username taken"
        }
    })
}

submitBtn.addEventListener("click", submitSignInHandler);