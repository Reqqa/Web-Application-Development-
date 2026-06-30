(function () {
  fetch("../src/checkauth.php", {
    credentials: "same-origin",
  })
    .then(function (res) {
      if (!res.ok) {
        throw new Error("not authenticated");
      }
      return res.json();
    })
    .then(function (data) {
      if (!data.authenticated) {
        window.location.href = "../src/auth.php";
      }
    })
    .catch(function () {
      window.location.href = "../src/auth.php";
    });
})();
