//Funkcja Dialog
function dialogInit() { // Inicjalizacja 

    tips = $(".validateTips");

    dialog = $("#dialog-form").dialog({ // Okno Dialogowe
        // Ustawienia Okna
        autoOpen: false,
        height: 700,
        width: 800,
        modal: true,
        buttons: {
            "OK": editImds, // Zatwierdzenie
            Cancel: function() {
                dialog.dialog("close"); // Zamknięcie
            }
        },
        close: function() {
            form[0].reset();
        }
    });

    form = dialog.find("form").on("submit", function(event) {
        event.preventDefault();
        editImds();
    });

    $("#add_row").button().on("click", function() { // Pojawienie się okna dialogowego
        edit_imds(0, 0);
    });
    return dialog;

    function updateTips(t) { // TIPS
        tips
            .text(t)
            .addClass("ui-state-highlight");
        setTimeout(function() {
            tips.removeClass("ui-state-highlight", 1500);
        }, 500);
    }

    function editImds() { // Tryb edycji wpisu

        $.ajax({
            url: base_url + 'getedit/setimds', // funkcja getedit -> model
            type: "GET", // Get
            data: $('#imds_form').serialize(), // Serialiacja
            success: function(data) {
                if (data['error'] != '') {
                    alert(data['error']); // Wyświetlanie błędu
                } else {
                    dialog.dialog("close");
                    $('#imds').DataTable().ajax.reload();
                    //zamkniecie okna edycji i przeładowanie tabeli
                }
            },
            error: function(jXHR, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
    }
};