import store from '@/store'
import { ref, watch } from '@vue/composition-api'
import { lang } from '@/common'
import { useRouter } from '@core/utils'
export default function useStore (){
  const listBoard = [
    {
      id: 1,
      name: 'Organization 1',
      children: [
        { id: 2, name: 'Organization 1-1' },
        { id: 3, name: 'Organization 1-2' },
        { id: 4, name: 'Organization 1-3' },
      ],
    },
    {
      id: 5,
      name: 'Documents :',
      children: [
        {
          id: 6,
          name: 'Organization 2',
          children: [
            {
              id: 7,
              name: 'Organization 2-1',
              children: [
                { id: 8, name: 'Organization 2-1-1' },
                { id: 9, name: 'Organization 2-1-2' },
              ],
            },
          ],
        },
        {
          id: 10,
          name: 'Organization 2-2',
          children: [
            {
              id: 11,
              name: 'src :',
              children: [
                { id: 12, name: 'Organization 2-2-1' },
                { id: 13, name: 'Organization 2-2-2' },
                { id: 14, name: 'Organization 2-2-3' },
              ],
            },
          ],
        },
      ],
    },
    {
      id: 15,
      name: 'Organization 3',
      children: [
        { id: 16, name: 'Organization 3-1' },
        { id: 17, name: 'Organization 3-2' },
        { id: 18, name: 'Organization 3-3' },
      ],
    },
  ]
  const lstFieldAdd =[
    {setting:trans('app.memo_setting')},
    {start:trans('app.start_date')},
    {end:trans('app.end_date')},
    {color:trans('app.color')},
    {file:trans('app.image_file_select')}
  ]
  const textDate = ref({
    startDate : trans('app.start_date'),
    endDate : trans('app.end_date')
  })
  return {
    listBoard,
    lstFieldAdd,textDate
  }
}

