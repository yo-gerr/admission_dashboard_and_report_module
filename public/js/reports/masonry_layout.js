function masonryLayout() {
  const container = document.querySelector(".dashboard");
  const items = Array.from(container.querySelectorAll(".card"));

  const columnWidth = 320;
  const columnGap = 20;
  const containerWidth = container.clientWidth;
  const columnCount = Math.max(1, Math.floor(containerWidth / (columnWidth + columnGap)));

  const totalGridWidth = columnCount * columnWidth + (columnCount - 1) * columnGap;
  const offsetX = (containerWidth - totalGridWidth) / 2;

  const columnHeights = new Array(columnCount).fill(0);
  const positionedItems = [];

  container.style.position = "relative";

  items.forEach(item => {
    const isWide = item.classList.contains("wide-card");
    const isCalendar = item.classList.contains("calendar-card");
    const isTable = item.classList.contains("table-card");
    const isDropdownOpen = item.classList.contains("dropdown-open");

    // Increase width if the card is wide AND a calendar or table card
    let itemWidth = columnWidth;
    if (isWide) {
      itemWidth += columnWidth + columnGap; // Default wide increase
      if (isCalendar || isTable) {
        itemWidth += columnWidth; // Extra width increase
      }
    }

    let x = 0;
    let y = 0;

    if (isWide && columnCount >= 2) {
      let bestCol = 0;
      let bestHeight = Infinity;
      for (let i = 0; i < columnCount - 1; i++) {
        const height = Math.max(columnHeights[i], columnHeights[i + 1]);
        if (height < bestHeight) {
          bestHeight = height;
          bestCol = i;
        }
      }

      x = offsetX + bestCol * (columnWidth + columnGap) + 1;
      y = Math.max(columnHeights[bestCol], columnHeights[bestCol + 1]);

      const newHeight = y + item.offsetHeight + columnGap;
      columnHeights[bestCol] = newHeight;
      columnHeights[bestCol + 1] = newHeight;
    } else {
      const col = columnHeights.indexOf(Math.min(...columnHeights));
      x = offsetX + col * (columnWidth + columnGap);
      y = columnHeights[col];

      columnHeights[col] += item.offsetHeight + columnGap;
    }

    item.style.position = "absolute";
    item.style.width = `${itemWidth}px`;
    item.style.transform = `translate(${x}px, ${y}px)`;

    // 👉 Apply z-index so dropdown cards stay on top
    item.style.zIndex = isDropdownOpen ? "9999" : "1";

    positionedItems.push(item);
  });

  const maxHeight = Math.max(...columnHeights);
  container.style.height = `${maxHeight}px`;
}

// Trigger on load and resize
window.onload = masonryLayout;
window.onresize = masonryLayout;
