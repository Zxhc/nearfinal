document.addEventListener("DOMContentLoaded", function () {
  const wrapper = document.getElementById("chartsWrapper");
  if (!wrapper || typeof allChartData === "undefined" || Object.keys(allChartData).length === 0) return;

  const isMonthly = currentView === "monthly";

  if (isMonthly) {
    // --- MONTHLY VIEW ---
    const container = document.createElement("div");
    container.className = "item-card";
    container.style.height = "100%";
    container.innerHTML = `<canvas id="mainMonthlyChart"></canvas>`;
    wrapper.appendChild(container);

    const now = new Date();
    const daysInMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0).getDate();
    const dateKeys = Array.from({length: daysInMonth}, (_, i) => 
        `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, "0")}-${String(i + 1).padStart(2, "0")}`);

    const colors = ["#db3434", "#3498db", "#2ecc71", "#f1c40f", "#9b59b6"];

    const datasets = Object.keys(allChartData).map((item, index) => ({
      label: item,
      data: dateKeys.map((key) => allChartData[item].stats[key] || 0),
      borderColor: colors[index % colors.length],
      borderWidth: 2,
      tension: 0.3,
      fill: false,
    }));

    new Chart(document.getElementById("mainMonthlyChart"), {
      type: "line",
      data: { labels: Array.from({length: daysInMonth}, (_, i) => i + 1), datasets },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 } } // No Decimals
        }
      }
    });

  } else {
    // --- WEEKLY VIEW ---
    const days = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
    wrapper.style.display = "grid";
    wrapper.style.gridTemplateColumns = "repeat(auto-fit, minmax(300px, 1fr))";

    Object.keys(allChartData).forEach((item) => {
      const chartDiv = document.createElement("div");
      chartDiv.className = "item-card";

      const canvasId = `chart-${item.replace(/[^a-z0-9]/gi, "-")}`;
      
      chartDiv.innerHTML = `
                <div class="item-info">
                    <span class="item-name">${item}</span>
                    <p class="item-description">${allChartData[item].desc}</p>
                </div>
                <div style="height:250px;"><canvas id="${canvasId}"></canvas></div>
            `;
      wrapper.appendChild(chartDiv);

      new Chart(document.getElementById(canvasId), {
        type: "line",
        data: {
          labels: days,
          datasets: [{
            label: "Usage",
            data: allChartData[item].stats,
            borderColor: "#072d7abd",
            backgroundColor: "rgba(92, 157, 203, 0.1)",
            fill: true,
            tension: 0.4,
          }],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { display: false } },
          scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 } }, // No Decimals
            x: { grid: { display: false } }
          }
        },
      });
    });
  }
});