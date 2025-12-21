function tryLogin() {
    let un = $("#txtUsername").val();
    let pw = $("#txtPassword").val();

    if (un.trim() !== "" && pw.trim() !== "") {
        $.ajax({
            url: "/ajaxhandler/loginAjax.php", // ✅ absolute path
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
                    // ✅ absolute redirect
                    window.location.href = "/attendance.php";
                } else {
                    $("#diverror").addClass("applyerrordiv");
                    $("#errormessage").text(rv.status);
                }
            },
            error: function (xhr, status, err) {
                let msg = "Server error";

                try {
                    let parsed = JSON.parse(xhr.responseText);
                    msg = parsed.message || parsed.status || xhr.responseText;
                } catch (e) {
                    msg = xhr.responseText || msg;
                }

                $("#lockscreen").removeClass("applylockscreen");
                $("#diverror").addClass("applyerrordiv");
                $("#errormessage").text(msg);

                console.error("Login AJAX error:", status, err, xhr.responseText);
            }
        });
    }
}

$(function () {
    $(document).on("keyup", "input", function () {
        $("#diverror").removeClass("applyerrordiv");

        let un = $("#txtUsername").val();
        let pw = $("#txtPassword").val();

        if (un.trim() !== "" && pw.trim() !== "") {
            $("#btnLogin").removeClass("inactivecolor").addClass("activecolor");
        } else {
            $("#btnLogin").removeClass("activecolor").addClass("inactivecolor");
        }
    });

    $(document).on("click", "#btnLogin", function () {
        tryLogin();
    });
});
