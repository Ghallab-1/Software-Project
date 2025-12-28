function tryLogin() {
    let un = $("#txtUsername").val();
    let pw = $("#txtPassword").val();

    if (un.trim() !== "" && pw.trim() !== "") {
        $.ajax({
            url: "/ajaxhandler/loginAjax.php",
            type: "POST",
            dataType: "json",
            data: {
                user_name: un,
                password: pw,
                action: "verifyUser"
            },
            beforeSend: function () {
                $("#diverror").removeClass("applyerrordiv");
                $("#lockscreen").addClass("applylockscreen");
            },
            success: function (rv) {
                $("#lockscreen").removeClass("applylockscreen");

                if (rv.status === "ALL OK") {
                    window.location.href = "/attendance.php";
                } else {
                    $("#diverror").addClass("applyerrordiv");
                    $("#errormessage").text(rv.message || rv.status);
                }
            },
            error: function (xhr) {
                $("#lockscreen").removeClass("applylockscreen");
                $("#diverror").addClass("applyerrordiv");
                $("#errormessage").text(xhr.responseText || "Server error");
            }
        });
    }
}

$(document).on("click", "#btnLogin", tryLogin);
