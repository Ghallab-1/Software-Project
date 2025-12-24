/*
Reusable AJAX template
$.ajax({
    url:"../ajaxhandler/attendanceAJAX.php",
    type:"POST",
    dataType:"json",
    data:{},
    success:function(rv){},
    error:function(xhr, status, err){}
});
*/

function getSessionHTML(rv) {
    let x = `<option value="">SELECT ONE</option>`;
    for (let i = 0; i < rv.length; i++) {
        let cs = rv[i];
        x += `<option value="${cs.id}">${cs.year} ${cs.term}</option>`;
    }
    return x;
}

function loadSessions() {
    $.ajax({
        url: "../ajaxhandler/attendanceAJAX.php",
        type: "POST",
        dataType: "json",
        data: { action: "getSession" },
        success: function(rv) {
            $("#ddlclass").html(getSessionHTML(rv));
        },
        error: function(xhr, status, err) {
            console.error("getSession error", err);
        }
    });
}

/* ================= COURSES ================= */

function getCourseCardHTML(classlist) {
    let x = ``;

    for (let i = 0; i < classlist.length; i++) {
        let cc = classlist[i];
        let encoded = encodeURIComponent(JSON.stringify(cc));

        x += `
            <div class="classcard" data-classobject="${encoded}">
                ${cc.code}
            </div>
        `;
    }
    return x;
}

function fetchFacultyCourses(facid, sessionid) {
    $.ajax({
        url: "../ajaxhandler/attendanceAJAX.php",
        type: "POST",
        dataType: "json",
        data: {
            action: "getFacultyCourses",
            facid: facid,
            sessionid: sessionid
        },
        success: function(rv) {
            console.log("Courses:", rv);
            $("#classlistarea").html(getCourseCardHTML(rv));
        },
        error: function(xhr, status, err) {
            console.error("getFacultyCourses error", err);
        }
    });
}

/* ================= CLASS DETAILS ================= */

function getClassdetailsAreaHTML(classobject) {
    let d = new Date();
    let ondate = d.toISOString().split("T")[0];

    return `
        <div class="classdetails">
            <div class="code-area">${classobject.code}</div>
            <div class="title-area">${classobject.title}</div>
            <div class="ondate-area">
                <input type="date" id="dtpondate" value="${ondate}">
            </div>
        </div>
    `;
}

/* ================= STUDENTS ================= */

function getStudentListHTML(studentList) {
    let x = `<div class="studenttlist"><label>STUDENT LIST</label></div>`;

    for (let i = 0; i < studentList.length; i++) {
        let cs = studentList[i];
        let checked = cs.isPresent === "YES" ? "checked" : "";
        let color = cs.isPresent === "YES" ? "presentcolor" : "absentcolor";

        x += `
            <div class="studentdetails ${color}" id="student${cs.id}">
                <div class="slno-area">${i + 1}</div>
                <div class="rollno-area">${cs.roll_no}</div>
                <div class="name-area">${cs.name}</div>
                <div class="checkbox-area">
                    <input type="checkbox" class="cbpresent"
                           data-studentid="${cs.id}" ${checked}>
                </div>
            </div>
        `;
    }

    x += `
        <div class="reportsection">
            <button id="btnReport">REPORT</button>
        </div>
        <div id="divReport"></div>
    `;

    return x;
}

function fetchStudentList(sessionid, classid, facid, ondate) {
    $.ajax({
        url: "../ajaxhandler/attendanceAJAX.php",
        type: "POST",
        dataType: "json",
        data: {
            action: "getStudentList",
            sessionid,
            classid,
            facid,
            ondate
        },
        success: function(rv) {
            $("#studentlistarea").html(getStudentListHTML(rv));
        },
        error: function(xhr, status, err) {
            console.error("getStudentList error", err);
        }
    });
}

/* ================= SAVE ATTENDANCE ================= */

function saveAttendance(studentid, courseid, facultyid, sessionid, ondate, ispresent) {
    $.ajax({
        url: "../ajaxhandler/attendanceAJAX.php",
        type: "POST",
        dataType: "json",
        data: {
            action: "saveattendance",
            studentid,
            courseid,
            facultyid,
            sessionid,
            ondate,
            ispresent
        },
        success: function() {
            let row = $("#student" + studentid);
            row.toggleClass("presentcolor", ispresent === "YES");
            row.toggleClass("absentcolor", ispresent === "NO");
        }
    });
}

/* ================= REPORT ================= */

function downloadCSV(sessionid, classid, facid) {
    $.ajax({
        url: "../ajaxhandler/attendanceAJAX.php",
        type: "POST",
        dataType: "json",
        data: {
            action: "downloadReport",
            sessionid,
            classid,
            facid
        },
        success: function(rv) {
            $("#divReport").html(
                `<object data="${rv.filename}" type="text/html"></object>`
            );
        }
    });
}

/* ================= INIT ================= */

$(function() {

    loadSessions();

    $(document).on("change", "#ddlclass", function() {
        $("#classlistarea, #classdetailsarea, #studentlistarea").html("");
        let sessionid = $(this).val();
        if (sessionid !== "") {
            fetchFacultyCourses($("#hiddenFacId").val(), sessionid);
        }
    });

    $(document).on("click", ".classcard", function() {
        let classobject = JSON.parse(
            decodeURIComponent($(this).attr("data-classobject"))
        );

        $("#hiddenSelectedCourseID").val(classobject.id);
        $("#classdetailsarea").html(getClassdetailsAreaHTML(classobject));

        fetchStudentList(
            $("#ddlclass").val(),
            classobject.id,
            $("#hiddenFacId").val(),
            $("#dtpondate").val()
        );
    });

    $(document).on("click", ".cbpresent", function() {
        saveAttendance(
            $(this).data("studentid"),
            $("#hiddenSelectedCourseID").val(),
            $("#hiddenFacId").val(),
            $("#ddlclass").val(),
            $("#dtpondate").val(),
            this.checked ? "YES" : "NO"
        );
    });

    $(document).on("change", "#dtpondate", function() {
        fetchStudentList(
            $("#ddlclass").val(),
            $("#hiddenSelectedCourseID").val(),
            $("#hiddenFacId").val(),
            $(this).val()
        );
    });

    $(document).on("click", "#btnReport", function() {
        downloadCSV(
            $("#ddlclass").val(),
            $("#hiddenSelectedCourseID").val(),
            $("#hiddenFacId").val()
        );
    });

    $(document).on("click", "#btnLogout", function() {
        $.ajax({
            url: "../ajaxhandler/logoutAjax.php",
            type: "POST",
            success: function() {
                location.replace("login.php");
            }
        });
    });
});
