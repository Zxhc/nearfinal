function animateValue(obj, start, end, duration) {
  let startTimestamp = null;
  const symbol = obj.innerText.replace(/[0-9.,]/g, "");

  const step = (timestamp) => {
    if (!startTimestamp) startTimestamp = timestamp;
    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
    const currentNumber = progress * (end - start) + start;

    obj.innerHTML =
      symbol +
      currentNumber.toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });

    if (progress < 1) {
      window.requestAnimationFrame(step);
    }
  };
  window.requestAnimationFrame(step);
}

document.addEventListener("DOMContentLoaded", () => {
  const counters = document.querySelectorAll(".count-up");

  counters.forEach((counter) => {
    const targetValue = parseFloat(counter.getAttribute("data-target")) || 0;
    animateValue(counter, 0, targetValue, 700);
  });
});
