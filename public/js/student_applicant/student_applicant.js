document.addEventListener("DOMContentLoaded", () => {
  const stepsContainer = document.querySelector(".steps");
  const circles = stepsContainer.querySelectorAll(".circle");
  const progressBar = document.querySelector(".indicator");

  const updateStepUI = (currentStep) => {
    circles.forEach((circle, index) => {
      circle.classList.toggle("active", index < currentStep);
    });

    const progress = ((currentStep - 1) / (circles.length - 1)) * 100;
    progressBar.style.width = `${progress}%`;
  };

  const fetchStatusStep = async () => {
      try {
          const response = await fetch(window.applicantStatusUrl); // Uses dynamically generated URL
          const data = await response.json();
          updateStepUI(data.step);
      } catch (error) {
          console.error("Error fetching step status:", error);
      }
  };

  // Initial fetch
  fetchStatusStep();

  // Auto-update every 5 seconds
  setInterval(fetchStatusStep, 5000);
});
