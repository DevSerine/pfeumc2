"use strict";
const navbar = document.querySelector("[data-navbar]"),
  overlay = document.querySelector("[data-overlay]"),
  header = document.querySelector("[data-header]"),
  backTopBtn = document.querySelector("[data-back-top-btn]"),
  searchPopup = document.getElementById("search-popup"),
  statsSection = document.querySelector(".section.stats"),
  navbarLinks = document.querySelectorAll(".navbar-link"),
  navTogglers = document.querySelectorAll("[data-nav-toggler]"),
  navLinks = document.querySelectorAll("[data-nav-link]"),
  statsData = {
    learnersCount: 50,
    coursesCompleted: 25,
    satisfactionRate: 100,
    trainersCount: 20,
  },
  addEventOnElem = (e, t, a) => {
    e instanceof NodeList || Array.isArray(e)
      ? e.forEach((e) => e.addEventListener(t, a))
      : e.addEventListener(t, a);
  },
  toggleNavbar = () => {
    navbar.classList.toggle("active"), overlay.classList.toggle("active");
  },
  closeNavbar = () => {
    navbar.classList.remove("active"), overlay.classList.remove("active");
  },
  scrollHandler = () => {
    let e = window.scrollY > 100;
    header.classList.toggle("active", e),
      backTopBtn.classList.toggle("active", e);
  },
  highlightActiveSection = () => {
    let e = document.querySelectorAll("section"),
      t = "";
    e.forEach((e) => {
      pageYOffset >= e.offsetTop - e.clientHeight / 3 &&
        (t = e.getAttribute("id"));
    }),
      navbarLinks.forEach((e) => {
        e.classList.toggle("active", e.getAttribute("href").includes(t));
      });
  },
  toggleSearchBar = () => {
    searchPopup.style.display =
      "flex" === searchPopup.style.display ? "none" : "flex";
  },
  togglePopup = (e) => {
    let t = document.getElementById(`popup${e}`);
    t.style.display = "flex" === t.style.display ? "none" : "flex";
  },
  animateCounter = (e, t, a) => {
    let l = document.getElementById(e),
      s = 0,
      r = t / (a / 50),
      o = setInterval(() => {
        (s = Math.min(s + r, t)),
          (l.textContent = `${Math.round(s)}${
            "satisfactionRate" === e ? "%" : "+"
          }`),
          s === t && clearInterval(o);
      }, 50);
  },
  observer = new IntersectionObserver(
    (e, t) => {
      e.forEach((e) => {
        e.isIntersecting &&
          (Object.keys(statsData).forEach((e) => {
            animateCounter(e, statsData[e], 2e3);
          }),
          t.disconnect());
      });
    },
    { threshold: 0.5 }
  );
observer.observe(statsSection),
  addEventOnElem(navTogglers, "click", toggleNavbar),
  addEventOnElem(navLinks, "click", closeNavbar),
  window.addEventListener("scroll", () => {
    scrollHandler(), highlightActiveSection();
  });