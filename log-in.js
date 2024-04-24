const form = document.getElementById("log-in-form");
const usernameInput = document.getElementById("username");
const passwordInput = document.getElementById("password");

async function loginUser (credentials) {
    const response = await $.ajax("http://localhost/test-api/api/index.php/login",
        {data: "", method: "POST", headers: {Authorization:"Basic " + credentials}});

    return response;
}

function submitLogInHandler(event) {
    event.preventDefault();

    let credentials = usernameInput.value + ":" + passwordInput.value;
    credentials = btoa(credentials);

    loginUser(credentials).then(data => {
        sessionStorage.setItem("jwtToken", data.body.jwt);
        sessionStorage.setItem("username", data.body.username);
        window.location.href = "http://localhost/test-front/index.html";
    }).catch(err => {
        console.log(err);
    });
}


form.addEventListener("submit", submitLogInHandler);