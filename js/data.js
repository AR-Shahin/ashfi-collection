/**
 * Ashfi Collection – static content & product data.
 * Everything the storefront shows is driven from this file, so content can be
 * edited here without touching any HTML. The shape of `products` mirrors a
 * typical Laravel API resource, so it can later be replaced by fetch('/api/products').
 *
 * Images: the files in /images are illustrated placeholders. Replace the paths
 * in `images` with real product photography (JPG/WebP, 3:4 ratio, ~900x1200).
 */
window.ASHFI = {
  brand: {
    name: "Ashfi Collection",
    tagline: "Elegance in Every Thread.",
    taglineBn: "প্রতিটি পোশাকে আভিজাত্যের ছোঁয়া।",
    phone: "+880 1700-000000",
    whatsapp: "8801700000000",
    email: "hello@ashficollection.com",
    address: "House 00, Road 00, Dhaka, Bangladesh",
    hours: "Sat – Thu, 10:00 AM – 9:00 PM",
    facebook: "https://www.facebook.com/",   // TODO: replace with the Ashfi Collection page URL
    instagram: "https://www.instagram.com/"  // TODO: replace with the Ashfi Collection profile URL
  },

  announcements: [
    "Discover Your Style | Nationwide Delivery Available",
    "New Arrivals Every Week | Festive Collection Now Live",
    "Free Delivery on Orders Over ৳5,000"
  ],

  delivery: {
    insideDhaka: 70,
    outsideDhaka: 130,
    freeOver: 5000
  },

  /* Product ids shown in the homepage "Featured Products" grid, in order. */
  featuredIds: [
    "rosewood-chikankari-kurti", "maroon-georgette-three-piece", "blush-linen-two-piece", "banarasi-katan-saree",
    "emerald-embroidered-kurti", "teal-coord-palazzo-set", "sage-cotton-three-piece", "embroidered-georgette-orna"
  ],

  categories: [
    { slug: "kurti",      name: "Kurti",        image: "images/categories/kurti.svg",        blurb: "Everyday elegance" },
    { slug: "two-piece",   name: "Two Piece",    image: "images/categories/two-piece.svg",    blurb: "Effortless co-ords" },
    { slug: "three-piece", name: "Three Piece",  image: "images/categories/three-piece.svg",  blurb: "Complete occasion sets" },
    { slug: "saree",       name: "Saree",        image: "images/categories/saree.svg",        blurb: "Timeless drapes" },
    { slug: "orna",        name: "Orna",         image: "images/categories/orna.svg",         blurb: "Finishing touches" },
    { slug: "new",         name: "New Arrivals", image: "images/categories/new-arrivals.svg", blurb: "Fresh this season", href: "shop.html?filter=new" }
  ],

  /* price = regular price, salePrice = discounted price (omit or null if none) */
  products: [
    {
      id: "rosewood-chikankari-kurti", name: "Rosewood Chikankari Kurti", category: "kurti",
      price: 2450, salePrice: 1990, isNew: true, isBestSeller: false, createdAt: "2026-10-24",
      sizes: ["S", "M", "L", "XL", "XXL"],
      colors: [{ name: "Rosewood", hex: "#A4495F" }, { name: "Ivory", hex: "#F3EBDD" }],
      short: "Soft cotton kurti with delicate Chikankari-style floral work.",
      description: "A graceful everyday kurti in breathable cotton, finished with fine floral embroidery at the yoke and a gold-trimmed placket. Relaxed A-line fit that moves beautifully.",
      details: { Fabric: "Soft cotton blend", Fit: "Relaxed A-line", Length: "Knee length", Work: "Floral thread embroidery", Care: "Gentle hand wash, dry in shade" },
      images: ["images/products/rosewood-chikankari-kurti-1.svg", "images/products/rosewood-chikankari-kurti-2.svg", "images/products/rosewood-chikankari-kurti-3.svg"]
    },
    {
      id: "ivory-block-print-kurti", name: "Ivory Block Print Kurti", category: "kurti",
      price: 1650, salePrice: null, isNew: false, isBestSeller: true, createdAt: "2026-08-12",
      sizes: ["S", "M", "L", "XL"],
      colors: [{ name: "Ivory", hex: "#E9DEC6" }, { name: "Burgundy", hex: "#722F45" }],
      short: "Light handblock-style stripes on pure cotton.",
      description: "A breezy cotton kurti with a refined striped block print. Easy to dress up with an orna or keep simple for the office and weekends.",
      details: { Fabric: "100% cotton", Fit: "Straight", Length: "Below knee", Work: "Block print", Care: "Machine wash cold, gentle cycle" },
      images: ["images/products/ivory-block-print-kurti-1.svg", "images/products/ivory-block-print-kurti-2.svg", "images/products/ivory-block-print-kurti-3.svg"]
    },
    {
      id: "emerald-embroidered-kurti", name: "Emerald Embroidered Kurti", category: "kurti",
      price: 2850, salePrice: 2280, isNew: false, isBestSeller: true, createdAt: "2026-09-02",
      sizes: ["S", "M", "L", "XL", "XXL"],
      colors: [{ name: "Emerald", hex: "#1F5A4B" }, { name: "Wine", hex: "#6C1F3A" }],
      short: "Deep emerald with champagne gold embroidery.",
      description: "Rich emerald fabric with metallic gold embroidery across the body and a contrast hem border. A confident choice for dinners and small gatherings.",
      details: { Fabric: "Viscose blend", Fit: "A-line", Length: "Knee length", Work: "Metallic thread embroidery", Care: "Dry clean recommended" },
      images: ["images/products/emerald-embroidered-kurti-1.svg", "images/products/emerald-embroidered-kurti-2.svg", "images/products/emerald-embroidered-kurti-3.svg"]
    },
    {
      id: "mustard-handloom-kurti", name: "Mustard Handloom Kurti", category: "kurti",
      price: 1890, salePrice: 1590, isNew: true, isBestSeller: false, createdAt: "2026-10-28",
      sizes: ["M", "L", "XL"],
      colors: [{ name: "Mustard", hex: "#C99A2E" }],
      short: "Warm mustard handloom with a burgundy hem border.",
      description: "Textured handloom-style cotton in a warm mustard shade, lined with a burgundy border at the hem. Comfortable for long days.",
      details: { Fabric: "Handloom-style cotton", Fit: "Straight", Length: "Knee length", Work: "Woven dots with border", Care: "Hand wash separately" },
      images: ["images/products/mustard-handloom-kurti-1.svg", "images/products/mustard-handloom-kurti-2.svg", "images/products/mustard-handloom-kurti-3.svg"]
    },
    {
      id: "blush-linen-two-piece", name: "Blush Linen Two Piece Set", category: "two-piece",
      price: 3200, salePrice: 2690, isNew: true, isBestSeller: false, createdAt: "2026-10-30",
      sizes: ["S", "M", "L", "XL"],
      colors: [{ name: "Blush", hex: "#E3B7B0" }, { name: "Sand", hex: "#D9C7A3" }],
      short: "Breathable linen top with wide-leg palazzo.",
      description: "A coordinated top and palazzo in soft blush linen. Lightweight, comfortable and effortlessly elegant for daytime events.",
      details: { Fabric: "Linen blend", Includes: "Top + palazzo", Fit: "Relaxed", Work: "Contrast hem trim", Care: "Machine wash cold" },
      images: ["images/products/blush-linen-two-piece-1.svg", "images/products/blush-linen-two-piece-2.svg", "images/products/blush-linen-two-piece-3.svg"]
    },
    {
      id: "teal-coord-palazzo-set", name: "Teal Co-ord Palazzo Set", category: "two-piece",
      price: 2950, salePrice: null, isNew: false, isBestSeller: true, createdAt: "2026-07-20",
      sizes: ["S", "M", "L", "XL"],
      colors: [{ name: "Teal", hex: "#2E6F73" }, { name: "Plum", hex: "#5A2A4F" }],
      short: "Cool teal co-ord with fine gold dot print.",
      description: "A modern co-ord set with a subtle gold dot print and generous palazzo. Dresses up easily for festive evenings.",
      details: { Fabric: "Crepe blend", Includes: "Top + palazzo", Fit: "Relaxed", Work: "Printed dots", Care: "Gentle machine wash" },
      images: ["images/products/teal-coord-palazzo-set-1.svg", "images/products/teal-coord-palazzo-set-2.svg", "images/products/teal-coord-palazzo-set-3.svg"]
    },
    {
      id: "plum-printed-two-piece", name: "Plum Printed Two Piece", category: "two-piece",
      price: 2650, salePrice: 2250, isNew: false, isBestSeller: false, createdAt: "2026-09-15",
      sizes: ["M", "L", "XL", "XXL"],
      colors: [{ name: "Plum", hex: "#5A2A4F" }],
      short: "Deep plum floral print, easy and elegant.",
      description: "Rich plum with a delicate blush floral print. A comfortable two piece that photographs beautifully and wears even better.",
      details: { Fabric: "Soft viscose", Includes: "Top + pants", Fit: "Regular", Work: "Floral print", Care: "Hand wash cold" },
      images: ["images/products/plum-printed-two-piece-1.svg", "images/products/plum-printed-two-piece-2.svg", "images/products/plum-printed-two-piece-3.svg"]
    },
    {
      id: "maroon-georgette-three-piece", name: "Maroon Georgette Three Piece", category: "three-piece",
      price: 5450, salePrice: 4590, isNew: false, isBestSeller: true, createdAt: "2026-09-25",
      sizes: ["S", "M", "L", "XL", "XXL"],
      colors: [{ name: "Maroon", hex: "#7A2335" }, { name: "Midnight", hex: "#22304F" }],
      short: "Flowing georgette kameez set with gold borders.",
      description: "A complete festive set: embroidered georgette kameez, matching salwar and a gold-bordered dupatta. Made for weddings and celebrations.",
      details: { Fabric: "Georgette", Includes: "Kameez + salwar + dupatta", Fit: "Regular", Work: "Embroidery and gold border", Care: "Dry clean only" },
      images: ["images/products/maroon-georgette-three-piece-1.svg", "images/products/maroon-georgette-three-piece-2.svg", "images/products/maroon-georgette-three-piece-3.svg"]
    },
    {
      id: "sage-cotton-three-piece", name: "Sage Cotton Three Piece", category: "three-piece",
      price: 3850, salePrice: 3290, isNew: true, isBestSeller: false, createdAt: "2026-10-20",
      sizes: ["S", "M", "L", "XL"],
      colors: [{ name: "Sage", hex: "#8FA58B" }, { name: "Sand", hex: "#D9C7A3" }],
      short: "Soft sage cotton set with a cream dupatta.",
      description: "A calm, fresh three piece in sage cotton with delicate pinstripes. Comfortable enough for all day, polished enough for any occasion.",
      details: { Fabric: "Pure cotton", Includes: "Kameez + salwar + dupatta", Fit: "Regular", Work: "Pinstripe weave", Care: "Machine wash cold" },
      images: ["images/products/sage-cotton-three-piece-1.svg", "images/products/sage-cotton-three-piece-2.svg", "images/products/sage-cotton-three-piece-3.svg"]
    },
    {
      id: "navy-zari-three-piece", name: "Navy Zari Three Piece", category: "three-piece",
      price: 6200, salePrice: 5290, isNew: false, isBestSeller: false, createdAt: "2026-08-30",
      sizes: ["M", "L", "XL", "XXL"],
      colors: [{ name: "Navy", hex: "#22304F" }],
      short: "Midnight navy with subtle gold zari work.",
      description: "Deep navy fabric with tiny gold zari motifs and a statement dupatta. A refined choice for receptions and formal dinners.",
      details: { Fabric: "Silk blend", Includes: "Kameez + salwar + dupatta", Fit: "Regular", Work: "Zari motifs", Care: "Dry clean only" },
      images: ["images/products/navy-zari-three-piece-1.svg", "images/products/navy-zari-three-piece-2.svg", "images/products/navy-zari-three-piece-3.svg"]
    },
    {
      id: "banarasi-katan-saree", name: "Banarasi Katan Saree", category: "saree",
      price: 8900, salePrice: 7490, isNew: false, isBestSeller: true, createdAt: "2026-09-08",
      sizes: ["Free Size"],
      colors: [{ name: "Wine", hex: "#6C1F3A" }, { name: "Emerald", hex: "#1F5A4B" }],
      short: "Wine katan silk with a woven gold border and pallu.",
      description: "A luxurious wine saree with intricate woven motifs, a rich gold border and a statement pallu. Comes with an unstitched blouse piece.",
      details: { Fabric: "Katan silk blend", Length: "6.3 metres incl. blouse piece", Work: "Woven zari motifs", Blouse: "Unstitched, 0.8 m", Care: "Dry clean only" },
      images: ["images/products/banarasi-katan-saree-1.svg", "images/products/banarasi-katan-saree-2.svg", "images/products/banarasi-katan-saree-3.svg"]
    },
    {
      id: "jamdani-style-cotton-saree", name: "Jamdani-Style Cotton Saree", category: "saree",
      price: 4950, salePrice: null, isNew: false, isBestSeller: true, createdAt: "2026-07-30",
      sizes: ["Free Size"],
      colors: [{ name: "Natural", hex: "#D9C7A3" }, { name: "Rose", hex: "#C98B93" }],
      short: "Lightweight cotton with a classic burgundy border.",
      description: "A breathable cotton saree inspired by the Jamdani weaving tradition, with fine stripes and a contrast burgundy border. Comfortable in Bangladesh's warm weather.",
      details: { Fabric: "Fine cotton", Length: "6.3 metres incl. blouse piece", Work: "Woven stripes, contrast border", Blouse: "Unstitched, 0.8 m", Care: "Gentle hand wash" },
      images: ["images/products/jamdani-style-cotton-saree-1.svg", "images/products/jamdani-style-cotton-saree-2.svg", "images/products/jamdani-style-cotton-saree-3.svg"]
    },
    {
      id: "lilac-georgette-party-saree", name: "Lilac Georgette Party Saree", category: "saree",
      price: 3990, salePrice: 3390, isNew: true, isBestSeller: false, createdAt: "2026-10-27",
      sizes: ["Free Size"],
      colors: [{ name: "Lilac", hex: "#C9B6D9" }, { name: "Blush", hex: "#E3B7B0" }],
      short: "Pastel georgette with a burgundy satin border.",
      description: "A light pastel georgette saree with a subtle dot print and a burgundy border. Drapes beautifully and weighs next to nothing.",
      details: { Fabric: "Georgette", Length: "6.2 metres incl. blouse piece", Work: "Printed dots, satin border", Blouse: "Unstitched, 0.8 m", Care: "Dry clean recommended" },
      images: ["images/products/lilac-georgette-party-saree-1.svg", "images/products/lilac-georgette-party-saree-2.svg", "images/products/lilac-georgette-party-saree-3.svg"]
    },
    {
      id: "embroidered-georgette-orna", name: "Embroidered Georgette Orna", category: "orna",
      price: 1450, salePrice: 1190, isNew: true, isBestSeller: false, createdAt: "2026-10-29",
      sizes: ["Free Size"],
      colors: [{ name: "Dusty Rose", hex: "#D8A7A0" }, { name: "Ivory", hex: "#F3EBDD" }],
      short: "Soft georgette orna with a gold-embroidered border.",
      description: "A flowing georgette orna with a hand-finished embroidered border. Lifts any plain kurti or three piece instantly.",
      details: { Fabric: "Georgette", Length: "2.4 metres", Work: "Border embroidery", Finish: "Hand-finished edges", Care: "Hand wash cold" },
      images: ["images/products/embroidered-georgette-orna-1.svg", "images/products/embroidered-georgette-orna-2.svg", "images/products/embroidered-georgette-orna-3.svg"]
    },
    {
      id: "printed-chiffon-orna", name: "Printed Chiffon Orna", category: "orna",
      price: 990, salePrice: null, isNew: false, isBestSeller: false, createdAt: "2026-08-05",
      sizes: ["Free Size"],
      colors: [{ name: "Sky", hex: "#8DA9C4" }, { name: "Mint", hex: "#BCD3C6" }],
      short: "Light chiffon with a delicate dot print.",
      description: "A weightless chiffon orna with a delicate dot print. A simple, affordable way to refresh your favourite outfits.",
      details: { Fabric: "Chiffon", Length: "2.3 metres", Work: "Printed dots", Finish: "Rolled edges", Care: "Hand wash cold" },
      images: ["images/products/printed-chiffon-orna-1.svg", "images/products/printed-chiffon-orna-2.svg", "images/products/printed-chiffon-orna-3.svg"]
    }
  ],

  /* DEMO CONTENT – replace with real customer feedback before launch. */
  testimonials: [
    { name: "Nusrat J.", place: "Dhaka", rating: 5, text: "The fabric feels lovely and the colour matched the photos. Packaging was neat and delivery came the next day." },
    { name: "Tasnim A.", place: "Chattogram", rating: 5, text: "I ordered a three piece for a family wedding and got so many compliments. The fit and finishing felt premium." },
    { name: "Farzana R.", place: "Sylhet", rating: 4, text: "Beautiful saree at a fair price. Easy to order on my phone, and the team was quick to confirm." }
  ],

  districts: ["Bagerhat","Bandarban","Barguna","Barishal","Bhola","Bogura","Brahmanbaria","Chandpur","Chapainawabganj","Chattogram","Chuadanga","Cox's Bazar","Cumilla","Dhaka","Dinajpur","Faridpur","Feni","Gaibandha","Gazipur","Gopalganj","Habiganj","Jamalpur","Jashore","Jhalokathi","Jhenaidah","Joypurhat","Khagrachhari","Khulna","Kishoreganj","Kurigram","Kushtia","Lakshmipur","Lalmonirhat","Madaripur","Magura","Manikganj","Meherpur","Moulvibazar","Munshiganj","Mymensingh","Naogaon","Narail","Narayanganj","Narsingdi","Natore","Netrokona","Nilphamari","Noakhali","Pabna","Panchagarh","Patuakhali","Pirojpur","Rajbari","Rajshahi","Rangamati","Rangpur","Satkhira","Shariatpur","Sherpur","Sirajganj","Sunamganj","Sylhet","Tangail","Thakurgaon"]
};
