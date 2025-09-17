function loadPage() {
    $.fn.modal.Constructor.prototype._enforceFocus = function() {};
    let $navegacion = document.getElementById("navegacion");
    let general = new General();
    $navegacion.addEventListener("click", function (e) {
        let element = e.target;
        if (element.classList.contains("active-panel")) {
            for (const active of document.querySelectorAll(".active-panel")) {
                if (active != element) active.classList.remove("active-menu");
            }
            element.classList.toggle("active-menu");
        }
    });
    let btnMenu = document.getElementById("btn-menu");
    btnMenu.addEventListener("click", function (e) {
        e.stopPropagation();
        $navegacion.classList.toggle("menu-small");
        let verif = $navegacion.classList.contains("menu-small");
        localStorage.setItem("active_small_menu", verif);
        let liActive = document.querySelector(".activeli");
        for (const element of document.querySelectorAll(".active-menu")) {
            element.classList.remove("active-menu");
        }
        if (!verif) {
            liActive.parentElement.previousElementSibling.classList.toggle(
                "active-menu"
            );
        }
    });
    let liSubMenu = document.querySelectorAll(".menu-desplegable");
    let subMenuSmall = document.getElementById("contenido-submenu");
    for (const element of liSubMenu) {
        element.addEventListener("click", function (e) {
            if (
                $navegacion.classList.contains("menu-small") &&
                window.innerWidth > 600
            ) {
                e.stopImmediatePropagation();
                let position = element.getBoundingClientRect();
                let coneUl = element.children[1].cloneNode(true);
                subMenuSmall.innerHTML = "";
                subMenuSmall.classList.remove("d-none");
                subMenuSmall.append(coneUl);
                subMenuSmall.style.top = position.top + "px";
            }
        });
    }
    document.body.addEventListener("click", function (e) {
        subMenuSmall.classList.add("d-none");
        if (
            $navegacion.classList.contains("menu-small") &&
            window.innerWidth <= 600 &&
            e.target.closest("#navegacion") !== $navegacion
        ) {
            $navegacion.classList.remove("menu-small");
        }
    });
    let active_menu = localStorage.getItem("active_small_menu");
    if (
        active_menu !== null &&
        active_menu == "true" &&
        window.innerWidth > 600
    ) {
        $navegacion.classList.add("menu-small");
        for (const element of document.querySelectorAll(".active-menu")) {
            element.classList.remove("active-menu");
        }
    }
    let $cbUbigeos = document.querySelectorAll(".cb-ubigeo");
    for (const element of $cbUbigeos) {
        $(element).on("select2:select", async function (e) {
            let cbUbigeo = document.getElementById(e.target.dataset.info);
            if (!cbUbigeo) {
                return;
            }
            let consulta = e.target.dataset.consulta;
            if (consulta == "provincia") {
                const cbDistrito = document.getElementById(
                    e.target.dataset.infoDistrito
                );
                if (cbDistrito) {
                    cbDistrito.innerHTML = "";
                }
                if (e.target.dataset.infoSegundoDistrito) {
                    document.getElementById(
                        e.target.dataset.infoSegundoDistrito
                    ).innerHTML = "";
                }
            }
            try {
                let response =
                    consulta == "provincia"
                        ? await general.getProvincias(this.value)
                        : await general.getDistritos(this.value);
                transforData(response, cbUbigeo);
                if (e.target.dataset.segundaInfo) {
                    transforData(
                        response,
                        document.getElementById(e.target.dataset.segundaInfo)
                    );
                }
            } catch (error) {
                console.error(error);
                alertify.alert("Error", "Error al cargar los datos");
            }
        });
    }
    if ($(".cb-caserios")) {
        $(".cb-caserios").on("select2:select", async function (e) {
            let datosn = new FormData();
            datosn.append("distrito", $(this).val());
            let response = await general.funcfetch(
                window.location.origin + "/intranet/ubigeo/ciudades",
                datosn
            );
            let template = `<option></option>`;
            response.ciudades.forEach((c) => {
                template += `<option value="${c.id}">${c.nombre_ciudad}</option>`;
            });
            document.querySelector($(this).attr("data-ciudades")).innerHTML =
                template;
        });
    }
    function transforData(data, cbUbigeo) {
        let template = `<option value=""></option>`;
        data.forEach((e) => {
            template += `<option value="${e.id_ubigeo}">${e.nombre_ubigeo}</option>`;
        });
        cbUbigeo.innerHTML = template;
    }
    $(".cb-ubigeo").select2({
        theme: "bootstrap",
        width: "100%",
        placeholder: "Seleccione una opción",
    });
    $(".cb-mult-nuevo").select2({
        theme: "bootstrap",
        width: "100%",
        tags: true,
    });
    $(".cb-select2-tags").select2({
        theme: "bootstrap",
        width: "100%",
        placeholder: "Seleccione o agregue una opción",
        tags: true,
    });
    $(".simple-select2").select2({
        theme: "bootstrap",
        width: "100%",
        placeholder: "Seleccione una opción",
    });
    
    $(".cb-tipo-doc").on("change.select2", function (e) {
        general.validarMinMaxTipoDocumento(
            $(this).data("input"),
            $(this).val()
        );
    });
}
window.addEventListener("DOMContentLoaded", loadPage);
