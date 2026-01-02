/* ===============================
   HELPER
================================ */
function getAccent(index) {
  const colors = [
    "#D62828",
    "#FCBF49",
    "#003049",
    "#F77F00",
    "#6A4C93",
    "#2A9D8F",
  ];
  return colors[index % colors.length];
}

function limitWords(text, maxWords = 15) {
  if (!text) return "";
  const words = text.trim().split(/\s+/);
  return words.length > maxWords
    ? words.slice(0, maxWords).join(" ") + "..."
    : text;
}

/* ===============================
   VERTICAL AUTO SLIDER
================================ */
class VerticalSlider {
  constructor(products) {
    this.products = products;
    this.index = 0;
    this.duration = 4000;

    this.container = document.querySelector(".slide-container");
    this.title = document.querySelector(".product-title");
    this.desc = document.querySelector(".product-desc");

    this.init();
  }

  init() {
    if (!this.products || this.products.length === 0) return;
    this.render();
    this.updateText();
    this.auto();
  }

  render() {
    this.container.innerHTML = "";

    this.products.forEach((p, i) => {
      const slide = document.createElement("div");
      slide.className = "slide" + (i === 0 ? " active" : "");

      const wrapper = document.createElement("div");
      wrapper.className = "product-wrapper";

      const ring = document.createElement("div");
      ring.className = "bg-ring";

      const circle = document.createElement("div");
      circle.className = "bg-circle";

      circle.style.background = p.accent;
      ring.style.background = `${p.accent}55`;

      const img = document.createElement("img");
      img.src = p.img;
      img.alt = p.name;
      img.className = "slide-img";

      wrapper.append(ring, circle, img);
      slide.appendChild(wrapper);
      this.container.appendChild(slide);
    });

    this.slides = document.querySelectorAll(".slide");
  }

  updateText() {
    const p = this.products[this.index];
    this.title.textContent = p.name;
    this.desc.textContent = limitWords(p.desc, 15);
  }

  next() {
    this.slides[this.index].classList.remove("active");
    this.index = (this.index + 1) % this.products.length;
    this.slides[this.index].classList.add("active");
    this.updateText();
  }

  auto() {
    setInterval(() => this.next(), this.duration);
  }
}

/* ===============================
   INIT SLIDER FROM DATABASE
================================ */
document.addEventListener("DOMContentLoaded", () => {
  fetch("/routes/read.php")
    .then((res) => res.json())
    .then((data) => {
      if (!data || data.length === 0) return;

      const sliderProducts = data.map((p, i) => ({
        name: p.name,
        desc: p.description,
        img: p.img,
        accent: getAccent(i),
      }));

      new VerticalSlider(sliderProducts);
    })
    .catch((err) => console.error("Slider error:", err));
});
