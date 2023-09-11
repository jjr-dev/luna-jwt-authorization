document.querySelector("form").addEventListener("submit", (e) => {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    fetch("api/v1/users/auth", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            email: formData.get("email"),
            password: formData.get("password"),
        }),
    })
        .then(async (res) => {
            const status = res.status;
            res = await res.json();

            if (status !== 200) throw new Error(res.error);

            const { data } = res;

            sessionStorage.setItem("authorization-token", data.authorization);
            sessionStorage.setItem("refresh-token", data.refresh);

            alert("Acesso realizado");
        })
        .catch((err) => {
            alert(err);
        });
});

document.querySelector("button:not([type='submit'])").addEventListener("click", async (e) => {
    e.preventDefault();
    getAuthorizationToken()
        .then((authorization) => {
            alert(`Token de autorização: ${authorization}`);
        })
        .catch((err) => {
            alert(err);
        });
});

function getAuthorizationToken() {
    return new Promise(async (resolve, reject) => {
        const authorizationToken = sessionStorage.getItem("authorization-token");
        if (verifyJwtExpiration(authorizationToken)) return resolve(`Bearer ${authorizationToken}`);

        const refreshToken = sessionStorage.getItem("refresh-token");
        if (!verifyJwtExpiration(refreshToken)) return reject("Token de autenticação e recriação expirados");

        fetch(`api/v1/users/auth`, {
            method: "PUT",
            headers: {
                Authorization: `Bearer ${refreshToken}`,
            },
        })
            .then(async (res) => {
                const status = res.status;
                res = await res.json();

                if (status !== 200) throw new Error(res.error);

                const authorizationToken = res.data;

                sessionStorage.setItem("authorization-token", authorizationToken);

                resolve(`Bearer ${authorizationToken}`);
            })
            .catch((err) => {
                reject(err);
            });
    });
}

function verifyJwtExpiration(token) {
    const parse = parseJwt(token);

    let timestamp = new Date().getTime().toString();
    timestamp = timestamp.substring(0, timestamp.length - 3);

    return parseInt(parse.exp) - parseInt(timestamp) > 0;
}

function parseJwt(token) {
    var base64Url = token.split(".")[1];
    var base64 = base64Url.replace(/-/g, "+").replace(/_/g, "/");
    var jsonPayload = decodeURIComponent(
        window
            .atob(base64)
            .split("")
            .map(function (c) {
                return "%" + ("00" + c.charCodeAt(0).toString(16)).slice(-2);
            })
            .join("")
    );

    return JSON.parse(jsonPayload);
}
