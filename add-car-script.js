import getCookie from "./assets/helper.js";


const form = document.getElementById("add-car-form");

const signInLink = document.querySelector("#sign-in");
const logInLink = document.querySelector("#log-in");
const logOutLink = document.querySelector("#log-out");
const logOutButton = logOutLink.firstElementChild;

if (getCookie("jwt")) {
    logOutLink.classList.remove("hidden");

    logInLink.classList.add("hidden");
    signInLink.classList.add("hidden");
} else {
    alert("You must be logged in to add cars");
    window.location.href = "http://localhost/test-front/log-in.html";
}


function sanitizeInput(data) {
    const payload = {}
    let ok = true;

    for (const item of data) {
        let itemVal = item.value.trim();

        if (itemVal === "" && item.classList.contains("required")) {
            ok = false;
            item.classList.add("bad-input");
            item.classList.remove("good-input");
        }
        else if (itemVal.search("<") !== -1) {
            item.classList.add("bad-input");
            item.classList.remove("good-input");
            ok = false;
        }
        else {
            item.classList.add("good-input");
            item.classList.remove("bad-input");
            payload[item.id] = (item.id === "description" || item.id === "image") ? item.value : item.value.toUpperCase();
        }
    }

    return (ok) ? payload : false;
}

async function submitForm(event) {
    event.preventDefault();
    const items = document.querySelectorAll(".form-input");
    const payload = sanitizeInput(items);

    if(!payload) return;

    const jwt =getCookie("jwt");
    const username =getCookie("username")

    const response = await $.ajax("http://localhost/test-api/api/index.php/post",
        {data: {"payload" : payload, "username" : username, "token" : jwt}, method: "POST"});

    if (response.success) {
        alert("Success");
        window.location.href = "http://localhost/test-front/index.html";
    }
}


form.addEventListener("submit", submitForm);

logOutButton.addEventListener("click", () => {
    // sessionStorage.removeItem("jwtToken");
    // sessionStorage.removeItem("username");
    window.location.reload();
})
