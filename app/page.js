import { supabase } from '@/lib/supabaseClient'

export default async function Dashboard() {
    const { data } = await supabase.rpc('get_total_stock') //nanti kita buat function
    return <h1>Total Material: {data?.length || 0}</h1>
}