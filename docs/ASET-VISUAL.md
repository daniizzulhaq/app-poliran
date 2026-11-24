# Aset visual POLIRAN

Diperbarui 24 November 2026 dari tiga gambar yang diberikan pengguna.

| Aset | Sumber dan penggunaan |
| --- | --- |
| `public/images/logo-polres-tts.png` | Logo unggahan Polres TTS, latar transparan. Dipakai komponen identitas dan favicon di layout publik, autentikasi, serta dashboard. |
| `public/images/logo-polisi-bersama-masyarakat.png` | Logo unggahan Polisi Bersama Masyarakat, latar transparan. Dipakai di pengenalan program dan footer. |
| `public/images/kapolres-tts.jpg` | Salinan foto asli pengguna. Wajah, seragam, dan rincian foto tidak diedit. Area putih berlebih dibingkai dengan CSS; foto unggahan admin tetap diprioritaskan. |

Kedua PNG dibuat dengan alat bawaan **imagegen**, mode `background-extraction`, `transparent_background: true`. Hasil terpilih disalin ke direktori `public/images` proyek. Tidak membutuhkan API key.

Prompt logo Polres:

> Use case: background-extraction. Asset type: production website PNG. Remove only the white exterior background of the exact existing police crest in the input. Preserve every original shape, detail, text NUSA TENGGARA TIMUR, colors and white internal elements exactly. Keep the open gap between top banner and shield transparent. Tight crop with small padding. Do not redesign or reinterpret the logo. True alpha transparency, no shadow, no checkerboard.

Prompt logo Polisi Bersama Masyarakat:

> Use case: background-extraction. Asset type: production website PNG. Remove the white and pale cream/yellow background from this exact existing logo; retain ONLY the crest, clasped hands line art and all lettering, with exact original layout and colors. Text must stay exactly POLISI BERSAMA MASYARAKAT and MELINDUNGI, MENGAYOMI DAN MELAYANI. Remove black bottom border too. Tight crop with small padding. Do not redesign logo or change text. True alpha transparency, no shadow, no checkerboard.

Foto asli yang dipasang tidak melalui imagegen. Percobaan cutout tidak dipakai karena pembingkaian foto asli menjaga kesesuaian wajah dan seragam.

Saat deploy, sertakan `public/images`, template yang berubah, serta `public/build` terbaru. Tidak ada perubahan skema atau data database.
