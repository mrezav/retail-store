export const formatCurrency = (value: number, locale = 'id-ID', currency = 'IDR') => {
  return new Intl.NumberFormat(locale, {
    style: 'currency',
    currency: currency,
    minimumFractionDigits: 0, // hilangkan angka desimal
  }).format(value);
};

export const formatDateTime = (dateStr: string, locale = 'id-ID') => {
  const date = new Date(dateStr)
  return new Intl.DateTimeFormat(locale, {
    dateStyle:'long',
    timeStyle:'short',
    timeZone:'Asia/Jakarta'
  }).format(date);
}

export const formatDate = (dateStr: string, locale = 'id-ID') => {
  const date = new Date(dateStr)
  return new Intl.DateTimeFormat(locale, {
    dateStyle:'long',
    timeZone:'Asia/Jakarta'
  }).format(date);
}

export const terbilang = (value: number) => {
  const angka = [
    "", "satu", "dua", "tiga", "empat", "lima",
    "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"
  ];
  
  let result = "";
  if (value <= 0 || isNaN(value) || undefined=== value) {
    return "";
  }
  if (value < 12) {
    result = angka[value];
  }
  else if (value < 20) {
    result = terbilang(value - 10) + " belas";
  }
  else if (value < 100) {
    result = terbilang(Math.floor(value / 10)) + " puluh " + terbilang(value % 10);
  }
  else if (value < 200) {
    result = "seratus " + terbilang(value - 100);
  }
  else if (value < 1000) {
    result = terbilang(Math.floor(value / 100)) + " ratus " + terbilang(value % 100);
  }
  else if (value < 2000) {
    result = "seribu " + terbilang(value - 1000);
  }
  else if (value < 1000000) {
    result = terbilang(Math.floor(value / 1000)) + " ribu " + terbilang(value % 1000);
  }
  else if (value < 1000000000) {
    result = terbilang(Math.floor(value / 1000000)) + " juta " + terbilang(value % 1000000);
  }
  
  return result.trim();
}