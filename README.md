# 🎓 Patstāvīgas apmācības tīmekļa vietne "Edutech"

## 📚 Par projektu

EduTech ir moderna mācību satura pārvaldības sistēma, kas izstrādāta, lai atvieglotu mācību procesu gan skolotājiem, gan skolēniem. Platforma nodrošina iespēju veidot, pārvaldīt un koplietot izglītības saturu interaktīvā un lietotājdraudzīgā vidē.

## ⭐ Galvenās funkcijas

- **👨‍🎓 Lietotāju saskarne**
  - 👤 Personalizēts lietotāja profils
  - 🎥 Video materiālu skatīšanās
  - 📚 Kursu pārlūkošana
  - 📊 Mācību progresa sekošana
  - 💻 Interaktīva mācību vide

- **👨‍🏫 Skolotāju saskarne**
  - 📤 Video materiālu augšupielāde un pārvaldība
  - 🔗 YouTube video integrācija
  - ✏️ Kursu veidošana
  - 📈 Skolēnu progresa pārraudzība
  - 📝 Mācību materiālu rediģēšana

- **👨‍💼 Administratora saskarne**
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

## 💻 Sistēmas prasības

- ⚡ PHP >= 8.0
- 🟢 Node.js >= 14.0
- 📦 Composer
- 🗄️ MySQL

## 🚀 Instalācija

1. Klonējiet repozitoriju:
```bash
git clone [projekta-url]
```

2. Instalējiet PHP atbalstu:
```bash
composer install
```

3. Instalējiet JavaScript atbalstu:
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
