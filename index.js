const postList = document.querySelector("#post-list");
const postListRow = postList.querySelector("#post-list-row");
const postListTitle = postList.firstElementChild;

const signInLink = document.querySelector("#sign-in");
const logInLink = document.querySelector("#log-in");
const logOutLink = document.querySelector("#log-out");
const logOutButton = logOutLink.firstElementChild;

if (sessionStorage.getItem("jwtToken")) {
    logOutLink.classList.remove("hidden");

    logInLink.classList.add("hidden");
    signInLink.classList.add("hidden");
}


getCars().then(data => {
    displayList(data);
}).catch(err => {
    console.log(err, "catch");
    postListTitle.innerText = "Something went wrong!";
});

async function getCars() {
    let response = await $.ajax("http://localhost/test-api/api/index.php/get", {data: "", method: "GET"});
    return response.body;

}

async function deleteCar(id) {
    let response = await $.ajax("http://localhost/test-api/api/index.php/delete/" + id,
        {data: {"username" : sessionStorage.getItem("username"), "token" : sessionStorage.getItem("jwtToken")},method: "POST"});
    return response.data;
}

function displayList(cars) {
    if (!cars) {
        postListTitle.innerText = "No cars available";
        return;
    }
    postListTitle.innerText = "Available cars:";

    for (const car of cars) {
        const postItemCol = document.createElement("div");
        postItemCol.className = "col";
        postItemCol.id = car.id

        car.image = (car.image) ? car.image : "assets/images/default.png";

        postItemCol.innerHTML = `
            <div class="card" style="width: 250px; height: 400px">
                <img src="${car.image}" alt="Car image" class="card-img-top" style="min-height: 200px; height: 200px;"/>
                <div class="card-header bg-primary-subtle">
                    <div class="card-title">
                        <h5 class="">${car.brand} ${car.model} </h5>
                    </div>
                    <div class="card-subtitle"> 
                        <h4 class="card-subtitle">${car.price} &#8364</h4>
                    </div>
                </div>
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <p class="no-wrap">${car.year} * ${car.power}hp * ${car.engine}<sup>3</sup> * ${car.fuel}</p>
                    <button id="${car.id}" class="btn btn-danger">Delete</button>
                    <p class="no-wrap">Owner: ${car.owner}</p>
                </div>
            </div>
        `;

        postListRow.appendChild(postItemCol);
    }
}

function postClickHandler(event) {
    if (event.target.className !== "btn btn-danger") return;

    if (sessionStorage.getItem("jwtToken")) {
        if (confirm("Are you sure?")) {
            deleteCar(event.target.id).then(() => {
                location.reload();
            }).catch(err => {
                alert("Something went wrong! " + err.responseJSON.error);
            });
        }
    } else {
        alert("You must be logged in for this action!");
    }
}

postList.addEventListener("click", postClickHandler);

logOutButton.addEventListener("click", () => {
    sessionStorage.removeItem("jwtToken");
    sessionStorage.removeItem("username");
    window.location.reload();
})