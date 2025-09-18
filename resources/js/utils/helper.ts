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