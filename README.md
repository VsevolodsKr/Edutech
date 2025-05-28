# 🎓 Patstāvīgas apmācības tīmekļa vietne "Edutech"

## 📚 Par Projektu

EduTech ir moderna mācību satura pārvaldības sistēma, kas izstrādāta, lai atvieglotu mācību procesu gan skolotājiem, gan skolēniem. Platforma nodrošina iespēju veidot, pārvaldīt un koplietot izglītības saturu interaktīvā un lietotājdraudzīgā vidē.

## ⭐ Galvenās Funkcijas

- **👨‍🎓 Lietotāju Saskarne**
  - 👤 Personalizēts lietotāja profils
  - 🎥 Video materiālu skatīšanās
  - 📚 Kursu pārlūkošana
  - 📊 Mācību progresa sekošana
  - 💻 Interaktīva mācību vide

- **👨‍🏫 Skolotāju Saskarne**
  - 📤 Video materiālu augšupielāde un pārvaldība
  - 🔗 YouTube video integrācija
  - ✏️ Kursu veidošana
  - 📈 Skolēnu progresa pārraudzība
  - 📝 Mācību materiālu rediģēšana

- **👨‍💼 Administratora Saskarne**
  - 👥 Lietotāju kontu pārvaldība
  - 🛡️ Satura moderācijas rīki
  - 📊 Sistēmas statistikas pārskati
  - 🔒 Drošības iestatījumu kontrole
  - 📋 Lietotāju aktivitāšu monitorings

## 🛠️ Tehnoloģijas

- **⚙️ Backend:**
  - 🔧 Laravel (PHP framework)
  - 💾 MySQL datubāze

- **🎨 Frontend:**
  - ⚡ Vue.js
  - 🎯 Tailwind CSS
  - 📱 JavaScript

## 💻 Sistēmas Prasības

- ⚡ PHP >= 8.0
- 🟢 Node.js >= 14.0
- 📦 Composer
- 🗄️ MySQL

## 🚀 Instalācija

1. Klonējiet repozitoriju:
```bash
git clone [projekta-url]
```

2. Instalējiet PHP atkarības:
```bash
composer install
```

3. Instalējiet JavaScript atkarības:
```bash
npm install
```

4. Konfigurējiet .env failu:
```bash
cp .env.example .env
php artisan key:generate
```

5. Veiciet datubāzes migrācijas:
```bash
php artisan migrate
```

6. Palaidiet izstrādes serveri:
```bash
php artisan serve
npm run dev
```