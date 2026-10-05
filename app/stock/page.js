import { supabase } from './supabaseClient'
export async function StockMaterial() {
    const { data } = await supabase
        .from('material')
        .select('id, nama_material, transaksi(jumlah, jenis)')
    
    return (
    <table border="1">
        <tr><th>Nama</th><th>Stock</th></tr>
        {data?.map(m => {
            const stock = m.transaksi.reduce((acc, t) =>
            t.jenis === 'IN' ? acc + t.jumlah : acc - t.jumlah, 0)
            return <tr><td>{m.nama_material}</td><td>{stock}</td></tr>
        })}
    </table>
    )
}