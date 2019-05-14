<band-pdf-form :band-id="`${object.id}`" class="mt-1"
			   @pdf-uploaded="() => {object.pdf = 1;onUpdate(object);}"
			   :init-has-pdf="object.pdf ? true : false"></band-pdf-form>
