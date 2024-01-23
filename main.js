  document.addEventListener('DOMContentLoaded', (event) => {
    const faders = document.querySelectorAll('.fade-in');
  
    const appearOptions = {
      threshold: 0,
      rootMargin: "0px 0px -100px 0px"
    };
  
    const appearOnScroll = new IntersectionObserver(function(
      entries,
      appearOnScroll
    ) {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('appear');
          appearOnScroll.unobserve(entry.target);
        }
      });
    }, appearOptions);
  
    faders.forEach(fader => {
      appearOnScroll.observe(fader);
    });
  
    // Aplicar la clase .appear si el elemento está ya en el viewport
    faders.forEach(fader => {
      if(fader.getBoundingClientRect().top <= window.innerHeight) {
        fader.classList.add('appear');
      }
    });
  });
  

  // Hovering Sobre nosotros
  function hoverEffect(element) {
    element.style.transform = "scale(1.05)";
    element.style.boxShadow = "0 8px 15px rgba(0, 0, 0, 0.3)";
  }
  
  function hoverOut(element) {
    element.style.transform = "scale(1)";
    element.style.boxShadow = "none";
  }


// Selecciona todos los elementos con la clase 'service-item'
const serviceItems = document.querySelectorAll('.service-item');

// Agrega event listeners para el hover
serviceItems.forEach(item => {
  item.addEventListener('mouseover', () => {
    item.style.transform = 'scale(1.05)';
    item.style.zIndex = '10'; // Asegúrate de que el elemento se muestre por encima de otros
  });
  item.addEventListener('mouseout', () => {
    item.style.transform = 'scale(1)';
    item.style.zIndex = '1';
  });
});

// Soporte Rápido
document.addEventListener('DOMContentLoaded', function() {
  var supportButton = document.querySelector('.support-button');
  var supportWindow = document.querySelector('.support-window');
  var closeSupport = document.querySelector('.close-support');
  var helpOptions = document.querySelectorAll('.help-option');
  var supportMessages = document.querySelector('.support-messages');
  var backToOptionsButton = document.querySelector('.back-to-options');
  var quickHelp = document.querySelector('.quick-help');

  // Mostrar la ventana de Soporte Rápido
  supportButton.addEventListener('click', function() {
    supportWindow.style.display = 'block';
    supportWindow.style.transform = 'translateY(0)';
  });

  // Cerrar la ventana de Soporte Rápido
  closeSupport.addEventListener('click', function() {
    supportWindow.style.transform = 'translateY(100%)';
    setTimeout(() => { supportWindow.style.display = 'none'; }, 300);
  });

  // Manejar clic en opciones de ayuda rápida
  helpOptions.forEach(function(option) {
    option.addEventListener('click', function() {
      var problemType = this.getAttribute('data-problem');
      displaySupportMessage(problemType);
    });
  });

  // Función para mostrar el mensaje de soporte
  function displaySupportMessage(problem) {
    var message = getMessageForProblem(problem);
    clearSupportMessages();
    if (message) {
      appendMessageToSupportWindow(message);
      backToOptionsButton.style.display = 'block';
      quickHelp.style.display = 'none';
    } else {
      // Si es "Otros problemas", se redirige al usuario y no se necesita ocultar las opciones
      backToOptionsButton.style.display = 'none';
      quickHelp.style.display = 'block';
    }
  }

  // Obtener el mensaje de ayuda según el problema
  function getMessageForProblem(problem) {
    switch (problem) {
      case 'account_issue':
        return 'Si tienes problemas con tu cuenta, por favor verifica que tus datos estén actualizados en tu perfil.';
      case 'payment_issue':
        return 'Si tu pago no se procesó correctamente, verifica tu método de pago o contacta a tu banco para más información.';
      case 'technical_issue':
        return 'Para problemas técnicos, asegúrate de que estás utilizando la última versión de nuestra aplicación.';
      case 'other_issue':
        window.location.href = '/contacto.html'; // Asegúrate de que esta URL es correcta
        return ''; // No se necesita un mensaje porque la página cambiará
      default:
        return 'No estoy seguro de cuál es el problema. Por favor, proporciona más detalles.';
    }
  }

  // Limpiar mensajes anteriores de soporte
  function clearSupportMessages() {
    supportMessages.innerHTML = '';
  }

  // Añadir mensaje a la ventana de soporte
  function appendMessageToSupportWindow(message) {
    var messageDiv = document.createElement('div');
    messageDiv.classList.add('support-message');
    messageDiv.textContent = message;
    supportMessages.appendChild(messageDiv);
  }

  // Manejar clic en botón de volver
  backToOptionsButton.addEventListener('click', function() {
    clearSupportMessages();
    this.style.display = 'none'; // Ocultar botón de volver
    quickHelp.style.display = 'block'; // Muestra las opciones de ayuda rápida
  });
});


// Actualizar cantidad carrito
function actualizarContadorCarrito(nuevaCantidad) {
  document.getElementById('contador-carrito').innerText = nuevaCantidad;
}

// Lógica payment
// Función para restringir la entrada a solo letras
function soloLetras(e) {
  var key = e.keyCode || e.which,
      tecla = String.fromCharCode(key).toLowerCase(),
      letras = " áéíóúabcdefghijklmnñopqrstuvwxyz",
      especiales = [8, 37, 39, 46], // backspace y flechas
      tecla_especial = false;

  for (var i in especiales) {
      if (key == especiales[i]) {
          tecla_especial = true;
          break;
      }
  }

  if (letras.indexOf(tecla) == -1 && !tecla_especial) {
      return false;
  }
}

// Función para restringir la entrada a solo números
function soloNumeros(e) {
  var key = e.keyCode || e.which,
      tecla = String.fromCharCode(key),
      numeros = "0123456789",
      especiales = [8, 37, 39, 46], // backspace y flechas
      tecla_especial = false;

  for (var i in especiales) {
      if (key == especiales[i]) {
          tecla_especial = true;
          break;
      }
  }

  if (numeros.indexOf(tecla) == -1 && !tecla_especial) {
      return false;
  }
}

// Cookies
document.addEventListener("DOMContentLoaded", function() {
  var cookieConsent = localStorage.getItem("cookieConsent");
  if (!cookieConsent) {
      document.getElementById("cookieConsentContainer").style.display = "block";
  }

  document.getElementById("acceptCookieConsent").onclick = function() {
      localStorage.setItem("cookieConsent", "true");
      document.getElementById("cookieConsentContainer").style.display = "none";
  };

  document.getElementById("declineCookieConsent").onclick = function() {
      document.getElementById("cookieConsentContainer").style.display = "none";
  };
});
