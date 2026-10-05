require('dotenv').config()
const { createClient } = require('@supabase/supabase-js')

const supabase = createClient(
  process.env.SUPABASE_URL,
  process.env.SUPABASE_ANON_KEY
)

async function checkConnection() {
  const { data, error } = await supabase.from('nama_tabel_kamu').select('*').limit(1)
  if (error) console.log('Koneksi Error:', error.message)
  else console.log('Koneksi Berhasil! Data:', data)
}

checkConnection()